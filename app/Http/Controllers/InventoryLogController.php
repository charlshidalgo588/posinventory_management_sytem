<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class InventoryLogController extends Controller
{
    public function index()
{
    // ✅ TRUE STOCK IN (from logs)
    $totalStockIn = DB::table('inventory_logs')
        ->where('type', 'stock_in')
        ->sum('quantity');

    // ✅ TRUE STOCK OUT
    $totalStockOut = DB::table('inventory_logs')
        ->where('type', 'stock_out')
        ->sum('quantity');

    // ✅ MANUAL ADJUSTMENTS ONLY
    $manualAdjustments = DB::table('inventory_logs')
        ->where('type', 'adjustment')
        ->count();

    // FULL LOGS
    $inventoryLogs = DB::table('inventory_logs')
        ->join('products', 'inventory_logs.ProductID', '=', 'products.ProductID')
        ->join('categories', 'products.CategoryID', '=', 'categories.CategoryID')
        ->select(
            'inventory_logs.*',
            'products.ProductName',
            'products.SKU',
            'categories.CategoryName'
        )
        ->orderBy('inventory_logs.created_at', 'desc')
        ->get();

    $summary = [
        'total_adjustments'  => $manualAdjustments,
        'total_stock_in'     => $totalStockIn,
        'total_stock_out'    => $totalStockOut,
        'manual_adjustments' => $manualAdjustments,
    ];

    return response()->json([
        'summary'               => $summary,
        'recent_activity'       => $inventoryLogs->take(5)->values(),
        'top_adjusted_products' => DB::table('inventory_logs')
            ->join('products', 'inventory_logs.ProductID', '=', 'products.ProductID')
            ->select(
                'products.ProductID',
                'products.ProductName',
                'products.SKU',
                DB::raw('COUNT(*) as adjustment_count'),
                DB::raw('SUM(CASE WHEN type = "stock_in" THEN quantity ELSE 0 END) as total_stock_in'),
                DB::raw('SUM(CASE WHEN type = "stock_out" THEN quantity ELSE 0 END) as total_stock_out')
            )
            ->groupBy('products.ProductID', 'products.ProductName', 'products.SKU')
            ->orderBy('adjustment_count', 'desc')
            ->limit(5)
            ->get(),
        'inventory_logs'        => $inventoryLogs,
    ], 200);
}

}
