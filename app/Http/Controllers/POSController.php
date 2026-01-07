<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display the point of sale interface.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::all();
        $products = Product::with(['category', 'inventory'])
            ->whereHas('inventory', function($query) {
                $query->where('QuantityOnHand', '>', 0);
            })
            ->get();

        return view('sales.point-of-sale', compact('categories', 'products'));
    }

    /**
     * PROCESS POS CHECKOUT
     * Saves sale, sale items & deducts stock.
     */
    public function checkout(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. Create the sale entry
            $sale = Sale::create([
                'customer_name'   => $request->customer_name,
                'subtotal'        => $request->subtotal,
                'vat'             => $request->vat,
                'discount'        => $request->discount,
                'total'           => $request->total,
                'payment_method'  => $request->payment_method,
                'amount_received' => $request->amount_received,
                'change'          => $request->change,
            ]);

            // 2. Loop through cart items
            foreach ($request->items as $item) {

                // Save item details
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['total'],
                ]);

                // Deduct from inventory
                $product = Product::with('inventory')->find($item['product_id']);

                if ($product && $product->inventory) {
                    $product->inventory->QuantityOnHand -= $item['quantity'];
                    $product->inventory->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Sale saved successfully!',
                'sale_id' => $sale->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error saving sale.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
