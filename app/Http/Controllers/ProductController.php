<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * =========================================================
     * PRODUCT LIST (USED BY POS + PRODUCT LIST PAGE)
     * =========================================================
     */
    public function productList()
    {
        $products = Product::with(['category', 'inventory'])
            ->orderBy('ProductID', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'ProductID'     => $p->ProductID,
                    'ProductName'   => $p->ProductName,
                    'SKU'           => $p->SKU,
                    'SellingPrice'  => (float) $p->SellingPrice,
                    'CostPrice'     => (float) $p->CostPrice,
                    'Brand'         => $p->Brand,
                    'Description'   => $p->Description,

                    // SPECIFICATIONS
                    'Material'      => $p->Material,
                    'ProfileType'   => $p->ProfileType,
                    'Color'         => $p->Color,
                    'Length'        => $p->Length,
                    'LengthUnit'    => $p->LengthUnit,
                    'Width'         => $p->Width,
                    'WidthUnit'     => $p->WidthUnit,
                    'Thickness'     => $p->Thickness,

                    // CATEGORY
                    'category' => [
                        'CategoryID'   => $p->category->CategoryID ?? null,
                        'CategoryName' => $p->category->CategoryName ?? null,
                    ],

                    // INVENTORY
                    'inventory' => [
                        'QuantityOnHand' => $p->inventory->QuantityOnHand ?? 0,
                        'ReorderLevel'   => $p->inventory->ReorderLevel ?? 0,
                    ],

                    // FLAT STOCK
                    'Stock' => $p->inventory->QuantityOnHand ?? 0,

                    // IMAGE
                    'Product_Image' => $p->Product_Image,
                    'ImageURL' => $p->Product_Image
                        ? asset('storage/' . $p->Product_Image)
                        : null,

                    // EXTRA
                    'CategoryID'   => $p->CategoryID,
                    'Unit'         => $p->Unit,
                    'Weight'       => $p->Weight,
                    'WeightUnit'   => $p->WeightUnit,
                    'IsReturnable' => (bool) $p->IsReturnable,
                    'created_at'   => $p->created_at,
                    'updated_at'   => $p->updated_at,
                ];
            });

        return response()->json($products);
    }

    /**
     * =========================================================
     * PRODUCT INDEX (MANAGEMENT)
     * =========================================================
     */
    public function index()
    {
        $products = Product::with(['category', 'inventory'])
            ->orderBy('ProductID', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'ProductID'    => $p->ProductID,
                    'ProductName'  => $p->ProductName,
                    'SKU'          => $p->SKU,
                    'SellingPrice' => (float) $p->SellingPrice,
                    'CostPrice'    => (float) $p->CostPrice,
                    'Stock'        => $p->inventory->QuantityOnHand ?? 0,
                    'CategoryName' => $p->category->CategoryName ?? null,
                    'CategoryID'   => $p->CategoryID,

                    // SPECIFICATIONS
                    'Material'     => $p->Material,
                    'ProfileType'  => $p->ProfileType,
                    'Color'        => $p->Color,
                    'Length'       => $p->Length,
                    'LengthUnit'   => $p->LengthUnit,
                    'Width'        => $p->Width,
                    'WidthUnit'    => $p->WidthUnit,

                    // IMAGE
                    'Product_Image' => $p->Product_Image,
                    'ImageURL' => $p->Product_Image
                        ? asset('storage/' . $p->Product_Image)
                        : null,
                ];
            });

        return response()->json($products);
    }

    /**
     * =========================================================
     * SHOW SINGLE PRODUCT
     * =========================================================
     */
    public function show($id)
    {
        $product = Product::with(['category', 'inventory', 'suppliers'])
            ->findOrFail($id);

        return response()->json([
    'ProductID'     => $product->ProductID,
    'ProductName'   => $product->ProductName,
    'SKU'           => $product->SKU,
    'Unit'          => $product->Unit,
    'Brand'         => $product->Brand,
    'Description'   => $product->Description,

    // SPECIFICATIONS
    'Material'      => $product->Material,
    'ProfileType'   => $product->ProfileType,
    'Color'         => $product->Color,

    'Length'        => $product->Length,
    'LengthUnit'    => $product->LengthUnit,   // ✅ ADD THIS
    'Width'         => $product->Width,
    'WidthUnit'     => $product->WidthUnit,    // ✅ ADD THIS
    'Thickness'     => $product->Thickness,

    'Weight'        => $product->Weight,
    'WeightUnit'    => $product->WeightUnit,

    'SellingPrice'  => (float) $product->SellingPrice,
    'CostPrice'     => (float) $product->CostPrice,
    'IsReturnable'  => (bool) $product->IsReturnable,

    'Product_Image' => $product->Product_Image,
    'ImageURL' => $product->Product_Image
        ? asset('storage/' . $product->Product_Image)
        : null,

    'category'  => $product->category,
    'inventory' => $product->inventory,
    'suppliers' => $product->suppliers,
]);

    }

    /**
     * =========================================================
     * CREATE PRODUCT
     * =========================================================
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create([
                'ProductName'   => $request->ProductName,
                'Unit'          => $request->Unit,
                'CategoryID'    => $request->CategoryID,
                'SKU'           => $request->SKU,
                'Brand'         => $request->Brand,
                'Description'   => $request->Description,

                // SPECIFICATIONS
                'Material'      => $request->Material,
                'ProfileType'   => $request->ProfileType,
                'Color'         => $request->Color,
                'Length'        => $request->Length,
                'LengthUnit'    => $request->LengthUnit,
                'Width'         => $request->Width,
                'WidthUnit'     => $request->WidthUnit,
                'Thickness'     => $request->Thickness,

                'Weight'        => $request->Weight,
                'WeightUnit'    => $request->WeightUnit,
                'SellingPrice'  => $request->SellingPrice,
                'CostPrice'     => $request->CostPrice,
                'IsReturnable'  => $request->boolean('IsReturnable'),

                'Product_Image' => $request->hasFile('Product_Image')
                    ? $request->file('Product_Image')->store('products', 'public')
                    : null,
            ]);

            // INVENTORY
            $product->inventory()->create([
                'QuantityOnHand' => $request->OpeningStock ?? 0,
                'ReorderLevel'   => $request->ReorderLevel,
                'LastUpdated'    => now(),
            ]);

            // SUPPLIER
            if ($request->SupplierID) {
                $product->suppliers()->attach($request->SupplierID, [
                    'PurchasePrice' => $request->PurchasePrice ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * =========================================================
     * UPDATE PRODUCT
     * =========================================================
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $product = Product::with('inventory')->findOrFail($id);

            $product->update([
                'ProductName'  => $request->ProductName,
                'Unit'         => $request->Unit,
                'CategoryID'   => $request->CategoryID,
                'Brand'        => $request->Brand,
                'Description'  => $request->Description,

                // SPECIFICATIONS
                'Material'     => $request->Material,
                'ProfileType'  => $request->ProfileType,
                'Color'        => $request->Color,
                'Length'       => $request->Length,
                'LengthUnit'   => $request->LengthUnit,
                'Width'        => $request->Width,
                'WidthUnit'    => $request->WidthUnit,
                'Thickness'    => $request->Thickness,

                'Weight'       => $request->Weight,
                'WeightUnit'   => $request->WeightUnit,
                'SellingPrice' => $request->SellingPrice,
                'CostPrice'    => $request->CostPrice,
                'IsReturnable' => $request->boolean('IsReturnable'),
            ]);

            if ($request->hasFile('Product_Image')) {
                if ($product->Product_Image) {
                    Storage::disk('public')->delete($product->Product_Image);
                }

                $product->update([
                    'Product_Image' => $request->file('Product_Image')->store('products', 'public')
                ]);
            }

            if ($request->stock_adjustment) {
                $product->inventory->QuantityOnHand += $request->stock_adjustment;
            }

            $product->inventory->ReorderLevel = $request->ReorderLevel;
            $product->inventory->LastUpdated  = now();
            $product->inventory->save();

            DB::commit();
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * =========================================================
     * RESTOCK PRODUCT
     * =========================================================
     */

public function restock(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $product = Product::with('inventory')->findOrFail($id);

        // Update inventory quantity
        $product->inventory->QuantityOnHand += $request->quantity;
        $product->inventory->LastUpdated = now();
        $product->inventory->save();

        // ✅ LOG STOCK IN (THIS WAS MISSING)
        DB::table('inventory_logs')->insert([
            'ProductID'  => $product->ProductID,
            'type'       => 'stock_in',
            'quantity'   => $request->quantity,
            'notes'      => 'Manual restock',
            'created_by' => auth()->id() ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * =========================================================
     * DELETE PRODUCT
     * =========================================================
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->Product_Image) {
            Storage::disk('public')->delete($product->Product_Image);
        }

        $product->suppliers()->detach();
        $product->inventory()->delete();
        $product->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * =========================================================
     * AUTO GENERATE SKU
     * =========================================================
     */
    public function generateSku($name)
    {
        if (!$name || strlen($name) < 2) {
            return response()->json(['sku' => '']);
        }

        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $prefix = str_pad($prefix, 3, 'X');

        $lastSku = Product::where('SKU', 'LIKE', "{$prefix}-%")
            ->orderBy('SKU', 'desc')
            ->value('SKU');

        $nextNumber = $lastSku
            ? (intval(substr($lastSku, 4)) + 1)
            : 1;

        return response()->json([
            'sku' => sprintf("%s-%05d", $prefix, $nextNumber)
        ]);
    }
}
