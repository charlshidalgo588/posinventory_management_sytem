<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BLADE VIEW METHODS (UNCHANGED – FOR ADMIN PANEL)
    |--------------------------------------------------------------------------
    */

    public function productTransactions($productId, Request $request)
    {
        $product = Product::findOrFail($productId);
        $products = Product::with(['inventory'])->get();

        $query = Transaction::where('ProductID', $productId)
            ->with(['sale.customer', 'sale.clerk', 'product.suppliers', 'purchase.supplier'])
            ->orderBy('TransactionDate', 'desc');

        $transactions = $query->get();

        // Sorting
        $sort = $request->get('sort', 'date_desc');
        match ($sort) {
            'date_asc'   => $transactions = $transactions->sortBy('TransactionDate'),
            'amount_asc' => $transactions = $transactions->sortBy('TotalAmount'),
            'amount_desc'=> $transactions = $transactions->sortByDesc('TotalAmount'),
            default      => $transactions = $transactions->sortByDesc('TransactionDate'),
        };

        // Pagination
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->forPage($request->get('page', 1), 10),
            $transactions->count(),
            10,
            $request->get('page', 1),
            ['path' => $request->url()]
        );

        // History
        $history = collect();
        $transactions->each(function ($t) use ($history) {
            $history->push((object)[
                'EventType' => 'STOCK_ADJUSTMENT',
                'EventDate' => $t->TransactionDate,
                'Description' => match ($t->TransactionType) {
                    'OPENING_STOCK' => 'Opening stock recorded',
                    'STOCK_PURCHASE' => 'Stock purchase recorded',
                    'SALE' => 'Sale recorded',
                    'RETURN' => 'Return recorded',
                    default => 'Transaction recorded',
                },
                'QuantityChange' => $t->QuantityChange,
                'OldValue' => null,
                'NewValue' => null,
            ]);
        });

        return view(
            'transaction.product-transaction',
            compact('product', 'products', 'transactions', 'history')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | JSON API METHODS (FOR VUE)
    |--------------------------------------------------------------------------
    */

    /**
     * ✅ API: Product transactions (Vue)
     */
    public function apiProductTransactions($productId, Request $request)
    {
        $product = Product::with('inventory')->findOrFail($productId);

        $query = Transaction::where('ProductID', $productId);

        /*
        |------------------------------------------------------------------
        | SEARCH (FIXED — DB-CORRECT)
        |------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $term = $request->search;

            $query->where(function ($q) use ($term) {
                $q->where('TransactionType', 'LIKE', "%{$term}%")
                  ->orWhereRaw(
                      "CONCAT(ReferenceType, '-', LPAD(ReferenceID, 6, '0')) LIKE ?",
                      ["%{$term}%"]
                  );
            });
        }

        /*
        |------------------------------------------------------------------
        | SORTING (UNCHANGED)
        |------------------------------------------------------------------
        */
        match ($request->get('sort', 'date_desc')) {
            'date_asc'    => $query->orderBy('TransactionDate', 'asc'),
            'amount_asc'  => $query->orderBy('TotalAmount', 'asc'),
            'amount_desc' => $query->orderBy('TotalAmount', 'desc'),
            default       => $query->orderBy('TransactionDate', 'desc'),
        };

        $transactions = $query->paginate(10);

        /*
        |------------------------------------------------------------------
        | TRANSFORM FOR VUE (REFERENCE FIXED)
        |------------------------------------------------------------------
        */
        $transactions->getCollection()->transform(fn ($t) => [
            'id'         => $t->TransactionID,
            'date'       => $t->TransactionDate,
            'type'       => $t->TransactionType,
            'quantity'   => (int) $t->QuantityChange,
            'unit_price' => (float) $t->UnitPrice,
            'total'      => (float) $t->TotalAmount,
            'reference'  => $t->ReferenceType && $t->ReferenceID
                ? strtoupper($t->ReferenceType) . '-' . str_pad($t->ReferenceID, 6, '0', STR_PAD_LEFT)
                : 'N/A',
        ]);

        return response()->json([
            'product' => [
                'id' => $product->ProductID,
                'name' => $product->ProductName,
                'sku' => $product->SKU,
                'price' => (float) $product->SellingPrice,
                'opening_stock' => (int) $product->OpeningStock,
                'quantity_on_hand' => (int) optional($product->inventory)->QuantityOnHand,
            ],
            'transactions' => $transactions,
        ]);
    }

    /**
     * ✅ API: Product summary cards (Vue)
     */
    public function apiProductSummary($productId)
    {
        return response()->json([
            'total_sales' => (float) Transaction::where('ProductID', $productId)
                ->where('TransactionType', 'SALE')
                ->sum('TotalAmount'),

            'total_returns' => (float) Transaction::where('ProductID', $productId)
                ->where('TransactionType', 'RETURN')
                ->sum('TotalAmount'),

            'units_sold' => (int) Transaction::where('ProductID', $productId)
                ->where('TransactionType', 'SALE')
                ->sum(DB::raw('ABS(QuantityChange)')),

            'units_returned' => (int) Transaction::where('ProductID', $productId)
                ->where('TransactionType', 'RETURN')
                ->sum(DB::raw('ABS(QuantityChange)')),
        ]);
    }

    /**
     * ✅ API: Product history (Vue History tab)
     */
    public function apiProductHistory($productId)
    {
        Product::findOrFail($productId);

        $history = Transaction::where('ProductID', $productId)
            ->orderBy('TransactionDate', 'desc')
            ->paginate(10);

        $history->getCollection()->transform(fn ($t) => [
            'event_type' => 'STOCK_ADJUSTMENT',
            'event_date' => $t->TransactionDate,
            'description' => match ($t->TransactionType) {
                'OPENING_STOCK' => 'Opening stock recorded',
                'STOCK_PURCHASE' => 'Stock purchase recorded',
                'SALE' => 'Sale recorded',
                'RETURN' => 'Return recorded',
                default => 'Transaction recorded',
            },
            'quantity_change' => (int) $t->QuantityChange,
            'old_value' => null,
            'new_value' => null,
        ]);

        return response()->json($history);
    }

    /*
    |--------------------------------------------------------------------------
    | EXISTING REPORT API (UNCHANGED)
    |--------------------------------------------------------------------------
    */

    public function getTransactionHistory(Request $request)
    {
        $query = Transaction::with(['product', 'sale']);

        if ($request->filled('type')) {
            $query->where('TransactionType', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('TransactionDate', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('TransactionDate', '<=', $request->end_date);
        }

        return response()->json(
            $query->orderBy('TransactionDate', 'desc')->paginate(15)
        );
    }

    public function getTransactionSummary()
    {
        return response()->json([
            'total_sales' => Transaction::where('TransactionType', 'SALE')
                ->whereDate('TransactionDate', today())
                ->sum('TotalAmount'),

            'total_quantity_sold' => Transaction::where('TransactionType', 'SALE')
                ->whereDate('TransactionDate', today())
                ->sum(DB::raw('ABS(QuantityChange)')),

            'top_selling_products' => Transaction::where('TransactionType', 'SALE')
                ->whereDate('TransactionDate', today())
                ->select('ProductID', DB::raw('SUM(ABS(QuantityChange)) as total_quantity'))
                ->groupBy('ProductID')
                ->orderByDesc('total_quantity')
                ->with('product')
                ->limit(5)
                ->get(),
        ]);
    }
}
