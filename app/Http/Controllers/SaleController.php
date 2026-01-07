<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Transaction;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\InventoryLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SaleController extends Controller
{
    /**
     * ============================================
     *  PROCESS SALE (POS)
     * ============================================
     */
    public function processSale(Request $request)
    {
        try {
            Log::info('POS Sale Request:', $request->all());

            $request->validate([
                'total_amount'     => 'required|numeric|min:0',
                'discount_amount'  => 'nullable|numeric|min:0',
                'amount_paid'      => 'required|numeric|min:0',
                'customer_name'    => 'required|string|max:255',
                'items'            => 'required|array|min:1',

                'items.*.product_id' => 'required|exists:products,ProductID',
                'items.*.quantity'   => 'required|integer|min:1',
                'items.*.price'      => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // 1. Create or find customer
            $customer = Customer::firstOrCreate(
                ['CustomerCode' => $request->customer_name],
                ['CustomerCode' => $request->customer_name]
            );

            // 2. Create sale header
            $sale = Sale::create([
                'SaleDate'       => now(),
                'CustomerID'     => $customer->CustomerID,
                'TotalAmount'    => $request->total_amount,
                'DiscountAmount' => $request->discount_amount ?? 0,
                'AmountPaid'     => $request->amount_paid,
                'PaymentMethod'  => 'Cash',
                'Status'         => 'Paid',
                'ClerkID'        => Auth::id() ?? 1,
            ]);

            // 3. Process sale items
            foreach ($request->items as $item) {
                // Sale Items
                $sale->salesItems()->create([
                    'ProductID'    => $item['product_id'],
                    'Quantity'     => $item['quantity'],
                    'PriceAtSale'  => $item['price'],
                    'Subtotal'     => $item['quantity'] * $item['price'],
                ]);

                // Transaction record
                Transaction::create([
                    'ProductID'       => $item['product_id'],
                    'TransactionType' => 'SALE',
                    'QuantityChange'  => -$item['quantity'],
                    'UnitPrice'       => $item['price'],
                    'TotalAmount'     => $item['quantity'] * $item['price'],
                    'ReferenceID'     => $sale->SaleID,
                    'TransactionDate' => now(),
                ]);

                // Deduct inventory
                $inventory = Inventory::where('ProductID', $item['product_id'])->first();
                if ($inventory) {
                    $inventory->QuantityOnHand -= $item['quantity'];
                    $inventory->save();
                }

                // Inventory log
                InventoryLog::create([
                    'ProductID'  => $item['product_id'],
                    'type'       => 'stock_out',
                    'quantity'   => $item['quantity'],
                    'notes'      => 'POS sale #' . $sale->SaleID,
                    'created_by' => Auth::id() ?? 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale processed successfully',
                'sale_id' => $sale->SaleID,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Sale Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================
     *  API: LIST SALES (for Vue)
     * ============================================
     */
    public function apiList()
    {
        $sales = Sale::with(['customer', 'clerk'])
            ->orderBy('SaleDate', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'SaleID'        => $sale->SaleID,
                    'SaleDate'      => $sale->SaleDate->format('M d, Y'),

                    // Customer name = CustomerCode
                    'CustomerName'  => $sale->customer->CustomerCode ?? 'Walk-in',

                    'TotalAmount'   => $sale->TotalAmount,
                    'PaymentMethod' => $sale->PaymentMethod ?? 'Cash',

                    // Status always returned
                    'Status'        => $sale->Status ?? 'Paid',

                    // Clerk name from users table
                    'ClerkName'     => $sale->clerk->name ?? 'Admin',
                ];
            });

        return response()->json($sales);
    }

    /**
     * ============================================
     *  API: SHOW A SINGLE SALE
     * ============================================
     */
    public function apiShow($id)
    {
        $sale = Sale::with(['customer', 'clerk', 'salesItems.product'])
            ->findOrFail($id);

        return response()->json($sale);
    }

    /**
     * ============================================
     *  BULK DELETE + RESTORE STOCK
     * ============================================
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'saleIds'       => 'required|array',
            'admin_password'=> 'required|string',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return response()->json(['message' => 'Invalid admin password'], 403);
        }

        DB::beginTransaction();

        try {
            $sales = Sale::with(['salesItems'])
                ->whereIn('SaleID', $request->saleIds)
                ->get();

            foreach ($sales as $sale) {

                foreach ($sale->salesItems as $item) {

                    // Restore Inventory
                    $inventory = Inventory::where('ProductID', $item->ProductID)->first();
                    if ($inventory) {
                        $inventory->QuantityOnHand += $item->Quantity;
                        $inventory->save();
                    }

                    InventoryLog::create([
                        'ProductID'  => $item->ProductID,
                        'type'       => 'stock_in',
                        'quantity'   => $item->Quantity,
                        'notes'      => 'Rollback of sale #' . $sale->SaleID,
                        'created_by' => Auth::id(),
                    ]);
                }

                $sale->salesItems()->delete();
                $sale->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Sales deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * ============================================
     *  DELETE SINGLE SALE (BACKEND PAGE)
     * ============================================
     */
    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();

            foreach ($sale->salesItems as $item) {

                $inventory = Inventory::where('ProductID', $item->ProductID)->first();
                if ($inventory) {
                    $inventory->QuantityOnHand += $item->Quantity;
                    $inventory->save();
                }

                InventoryLog::create([
                    'ProductID'  => $item->ProductID,
                    'type'       => 'stock_in',
                    'quantity'   => $item->Quantity,
                    'notes'      => 'Cancelled sale #' . $sale->SaleID,
                    'created_by' => Auth::id(),
                ]);
            }

            $sale->salesItems()->delete();
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')
                ->with('success', 'Sale deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
