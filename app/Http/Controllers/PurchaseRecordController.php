<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRecord;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRecordController extends Controller
{
    /**
     * Store a new purchase record and create corresponding transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'ProductID' => 'required|exists:products,ProductID',
            'Quantity' => 'required|integer|min:1',
            'UnitPrice' => 'required|numeric|min:0',
            'ReferenceNumber' => 'nullable|string',
            'Notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create purchase record
            $purchase = PurchaseRecord::create([
                'SupplierID' => $request->SupplierID,
                'ProductID' => $request->ProductID,
                'Quantity' => $request->Quantity,
                'UnitPrice' => $request->UnitPrice,
                'TotalAmount' => $request->Quantity * $request->UnitPrice,
                'ReferenceNumber' => $request->ReferenceNumber,
                'Notes' => $request->Notes
            ]);

            // Create transaction record
            Transaction::create([
                'ProductID' => $request->ProductID,
                'TransactionType' => 'STOCK_PURCHASE',
                'QuantityChange' => $request->Quantity,
                'UnitPrice' => $request->UnitPrice,
                'TotalAmount' => $request->Quantity * $request->UnitPrice,
                'ReferenceType' => 'purchase',
                'ReferenceID' => $purchase->PurchaseID,
                'Notes' => "Stock purchase from supplier (Ref: {$request->ReferenceNumber})"
            ]);

            // Update product inventory
            $product = Product::findOrFail($request->ProductID);
            $product->inventory()->update([
                'QuantityOnHand' => DB::raw('QuantityOnHand + ' . $request->Quantity)
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Purchase record created successfully',
                'purchase' => $purchase
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create purchase record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get purchase history for a product.
     */
    public function productPurchases($productId)
    {
        $purchases = PurchaseRecord::with(['supplier'])
            ->where('ProductID', $productId)
            ->orderBy('PurchaseDate', 'desc')
            ->paginate(10);

        return response()->json($purchases);
    }

    /**
     * Get purchase history for a supplier.
     */
    public function supplierPurchases($supplierId)
    {
        $purchases = PurchaseRecord::with(['product'])
            ->where('SupplierID', $supplierId)
            ->orderBy('PurchaseDate', 'desc')
            ->paginate(10);

        return response()->json($purchases);
    }
} 