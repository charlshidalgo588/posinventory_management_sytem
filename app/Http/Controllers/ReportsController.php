<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        /**
         * ============================================================
         * FIXED DATE CONTEXT (USE 2025 DATA)
         * ============================================================
         */
        $year  = 2025;
        $month = 12;

        $today = Carbon::create($year, $month, 1)->startOfMonth();

        /* ============================================================
         * SALES ACTIVITY
         * ========================================================== */

        $totalSales = DB::table('sales')
            ->whereYear('SaleDate', $year)
            ->whereMonth('SaleDate', $month)
            ->sum('TotalAmount') ?? 0;

        $itemsSold = DB::table('sales_items')
            ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
            ->whereYear('sales.SaleDate', $year)
            ->whereMonth('sales.SaleDate', $month)
            ->sum('sales_items.Quantity') ?? 0;

        $transactions = Sale::whereYear('SaleDate', $year)
            ->whereMonth('SaleDate', $month)
            ->count();

        $voids = Sale::whereYear('SaleDate', $year)
            ->whereMonth('SaleDate', $month)
            ->where('TotalAmount', 0)
            ->count();

        $monthlyProfit = DB::table('sales_items')
    ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
    ->join('products', 'sales_items.ProductID', '=', 'products.ProductID')
    ->whereYear('sales.SaleDate', $year)
    ->whereMonth('sales.SaleDate', $month)
    ->selectRaw('
        SUM((sales_items.PriceAtSale - products.CostPrice) * sales_items.Quantity) as profit
    ')
    ->value('profit') ?? 0;

$salesActivity = [
    [
        'label' => "This Month's Sales",
        'value' => '₱' . number_format($totalSales, 2),
    ],
    [
        'label' => "Items Sold This Month",
        'value' => (int) $itemsSold,
    ],
    [
        'label' => "Transactions This Month",
        'value' => (int) $transactions,
    ],
    [
        'label' => "Monthly Profit",
        'value' => '₱' . number_format($monthlyProfit, 2),
    ],
];



        /* ============================================================
         * INVENTORY SUMMARY
         * ========================================================== */

        $quantityInHand = Inventory::sum('QuantityOnHand');

        $lowStock = DB::table('products')
            ->join('inventories', 'products.ProductID', '=', 'inventories.ProductID')
            ->whereColumn('inventories.QuantityOnHand', '<=', 'products.ReorderLevel')
            ->count();

        $inventorySummary = [
            [
                'label' => 'Quantity in Hand',
                'value' => (int) $quantityInHand,
            ],
            [
                'label' => 'Quantity to Receive',
                'value' => 0,
            ],
            [
                'label' => 'Low Stock Items',
                'value' => (int) $lowStock,
                'isRed' => true,
            ],
            [
                'label' => 'Total Items',
                'value' => Product::count(),
            ],
            [
                'label' => 'Active Items',
                'value' => Product::whereHas('inventory', fn ($q) =>
                    $q->where('QuantityOnHand', '>', 0)
                )->count(),
            ],
        ];

        /* ============================================================
         * TOP SELLING ITEMS (MONTH)
         * ========================================================== */

        $topSelling = DB::table('sales_items')
            ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
            ->join('products', 'sales_items.ProductID', '=', 'products.ProductID')
            ->join('categories', 'products.CategoryID', '=', 'categories.CategoryID')
            ->whereYear('sales.SaleDate', $year)
            ->whereMonth('sales.SaleDate', $month)
            ->select(
                'products.ProductName',
                'categories.CategoryName',
                DB::raw('SUM(sales_items.Quantity) as total_quantity'),
                DB::raw('SUM(sales_items.Quantity * sales_items.PriceAtSale) as total_sales')
            )
            ->groupBy('products.ProductName', 'categories.CategoryName')
            ->orderByDesc('total_quantity')
            ->get();

        /* ============================================================
         * MONTHLY SALES (CHART)
         * ========================================================== */

        $monthlySales = Sale::whereYear('SaleDate', $year)
            ->whereMonth('SaleDate', $month)
            ->select(
                DB::raw('DATE(SaleDate) as date'),
                DB::raw('SUM(TotalAmount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlyTotal = $totalSales;

        /* ============================================================
         * SALES LIST (TRANSACTIONS PAGE)
         * ========================================================== */

        $salesList = Sale::with(['customer', 'clerk'])
            ->orderBy('SaleDate', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'SaleID'        => $sale->SaleID,
                    'SaleDate'      => $sale->SaleDate,
                    'CustomerName'  => $sale->customer->CustomerCode ?? 'Walk-in Customer',
                    'TotalAmount'   => $sale->TotalAmount,
                    'PaymentMethod' => $sale->PaymentMethod,
                    'Status'        => $sale->Status,
                    'ClerkName'     => $sale->clerk->name ?? 'Unknown',
                ];
            });

        /* ============================================================
         * RETURN JSON (MATCHES VUE 1:1)
         * ========================================================== */

        return response()->json([
            'sales_activity'    => $salesActivity,
            'inventory_summary' => $inventorySummary,
            'top_selling'       => $topSelling,
            'monthly_sales'     => $monthlySales,
            'monthly_total'     => $monthlyTotal,
            'sales'             => $salesList,
        ], 200);
    }
}
