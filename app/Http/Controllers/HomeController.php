<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today();

        // --- BASIC TODAY METRICS ---------------------------------------------
        $todaySales = DB::table('sales')
            ->whereDate('SaleDate', $today)
            ->select(DB::raw('ROUND(SUM(TotalAmount)) as total'))
            ->value('total') ?? 0;

        $itemsSoldToday = DB::table('sales_items')
            ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
            ->whereDate('sales.SaleDate', $today)
            ->sum('sales_items.Quantity');

        $transactionsToday = Sale::whereDate('SaleDate', $today)->count();
        $voidTransactionsToday = 0;

        // --- INVENTORY SUMMARY -----------------------------------------------
        $inventorySummary = [
            'quantity_in_hand'   => Inventory::sum('QuantityOnHand'),
            'quantity_to_receive'=> 0,
            'low_stock_items'    => DB::table('products')
                ->join('inventories', 'products.ProductID', '=', 'inventories.ProductID')
                ->whereRaw('inventories.QuantityOnHand <= (products.OpeningStock/2 - 1)')
                ->count(),
            'total_items'        => Product::count(),
            'active_items'       => Product::whereHas('inventory', function ($q) {
                $q->where('QuantityOnHand', '>', 0);
            })->count(),
        ];

        // --- DATE RANGE HANDLING ---------------------------------------------
        $dateRange = $request->input('date_range', 'this_month');
        $startDate = $this->getStartDate($dateRange)->startOfDay();
        $endDate   = $this->getEndDate($dateRange)->endOfDay();

        // --- TOTAL PROFIT -----------------------------------------------------
        $totalProfit = DB::table('sales_items')
            ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
            ->join('products', 'sales_items.ProductID', '=', 'products.ProductID')
            ->whereBetween('sales.SaleDate', [$startDate, $endDate])
            ->selectRaw('SUM((sales_items.PriceAtSale - products.CostPrice) * sales_items.Quantity) as profit')
            ->value('profit') ?? 0;

        // --- TOP SELLING ITEMS ------------------------------------------------
        $topSellingItems = DB::table('sales_items')
            ->join('products', 'sales_items.ProductID', '=', 'products.ProductID')
            ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
            ->leftJoin('categories', 'products.CategoryID', '=', 'categories.CategoryID')
            ->whereBetween('sales.SaleDate', [$startDate, $endDate])
            ->select(
                'products.ProductName',
                'categories.CategoryName',
                DB::raw('SUM(sales_items.Quantity) as total_quantity'),
                DB::raw('SUM(sales_items.Quantity * sales_items.PriceAtSale) as total_sales')
            )
            ->groupBy('products.ProductID', 'products.ProductName', 'categories.CategoryName')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // --- DAILY SALES ------------------------------------------------------
        $rawSales = Sale::whereBetween('SaleDate', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(SaleDate) as date'),
                DB::raw('SUM(TotalAmount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // --- DAILY PROFIT -----------------------------------------------------
        $rawProfit = DB::table('sales_items')
            ->join('sales', 'sales_items.SaleID', '=', 'sales.SaleID')
            ->join('products', 'sales_items.ProductID', '=', 'products.ProductID')
            ->whereBetween('sales.SaleDate', [$startDate, $endDate])
            ->selectRaw('DATE(sales.SaleDate) as date,
                SUM((sales_items.PriceAtSale - products.CostPrice) * sales_items.Quantity) as profit')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ----------------------------------------------------------------------
        // 🔥 FIX: Generate full date range & align sales + profit data
        // ----------------------------------------------------------------------
        $monthlySales = [];
        $dailyProfit  = [];

        $period = new \DatePeriod(
            Carbon::parse($startDate),
            new \DateInterval('P1D'),
            Carbon::parse($endDate)->addDay()
        );

        foreach ($period as $day) {
            $date = $day->format('Y-m-d');

            $monthlySales[] = [
                'date'  => $date,
                'total' => $rawSales[$date]->total ?? 0
            ];

            $dailyProfit[] = [
                'date'   => $date,
                'profit' => $rawProfit[$date]->profit ?? 0
            ];
        }

        // TOTAL SALES RANGE
        $monthlyTotal = DB::table('sales')
            ->whereBetween('SaleDate', [$startDate, $endDate])
            ->select(DB::raw('SUM(TotalAmount) as total'))
            ->value('total') ?? 0;

        // --- RETURN RESPONSE --------------------------------------------------
        return response()->json([
            'todaySales'        => $todaySales,
            'itemsSoldToday'    => $itemsSoldToday,
            'transactionsToday' => $transactionsToday,
            'voidTransactionsToday' => $voidTransactionsToday,

            'inventorySummary'  => $inventorySummary,
            'topSellingItems'   => $topSellingItems,

            // 🔥 FIXED data (perfectly aligned)
            'monthlySales'      => $monthlySales,
            'dailyProfit'       => $dailyProfit,

            'monthlyTotal'      => $monthlyTotal,
            'totalProfit'       => $totalProfit,
            'dateRange'         => $dateRange
        ]);
    }

    // ------------------ DATE RANGE HELPERS ---------------------

    private function getStartDate($dateRange)
    {
        return match ($dateRange) {
            'today'      => Carbon::today(),
            'yesterday'  => Carbon::yesterday(),
            'this_week'  => Carbon::now()->startOfWeek(),
            'last_week'  => Carbon::now()->subWeek()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            'last_month' => Carbon::now()->subMonth()->startOfMonth(),
            'this_year'  => Carbon::now()->startOfYear(),
            'last_year'  => Carbon::now()->subYear()->startOfYear(),
            default      => Carbon::now()->startOfMonth(),
        };
    }

    private function getEndDate($dateRange)
    {
        return match ($dateRange) {
            'today'      => Carbon::today()->endOfDay(),
            'yesterday'  => Carbon::yesterday()->endOfDay(),
            'this_week'  => Carbon::now()->endOfWeek(),
            'last_week'  => Carbon::now()->subWeek()->endOfWeek(),
            'this_month' => Carbon::now()->endOfMonth(),
            'last_month' => Carbon::now()->subMonth()->endOfMonth(),
            'this_year'  => Carbon::now()->endOfYear(),
            'last_year'  => Carbon::now()->subYear()->endOfYear(),
            default      => Carbon::now()->endOfMonth(),
        };
    }
}
