<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Account;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\ProductLocationStock;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        $locationId = auth()->user()->location_id;

        $thisMonthPurchases = Purchase::where('location_id', $locationId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
            
        $totalPurchases = Purchase::where('location_id', $locationId)->sum('total_amount');
        
        $thisMonthSales = Sale::where('location_id', $locationId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('net_amount');
            
        $totalSales = Sale::where('location_id', $locationId)->sum('net_amount');
        
        $customerBalance = Sale::where('location_id', $locationId)
            ->sum(DB::raw('net_amount - paid_amount'));
            
        $vendorBalance = Purchase::where('location_id', $locationId)
            ->sum(DB::raw('total_amount - paid_amount'));
            
        $totalStockValue = ProductLocationStock::where('location_id', $locationId)
            ->join('products', 'products.id', '=', 'product_location_stocks.product_id')
            ->sum(DB::raw('product_location_stocks.quantity * products.cost_price'));
            
        $selfBalance = \App\Models\Account::sum('balance');

        $salesCountThisMonth = Sale::where('location_id', $locationId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $expensesThisMonth = Expense::where('location_id', $locationId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $grossProfitThisMonth = SaleItem::whereHas('sale', function ($q) use ($locationId) {
                $q->where('location_id', $locationId)
                  ->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            })
            ->sum(DB::raw('(unit_price - cost_price) * quantity'));
            
        $netProfitThisMonth = $grossProfitThisMonth - $expensesThisMonth;

        $revenueDates = collect();
        $revenueData = collect();
        $expenseData = collect();
        $profitData = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $revenueDates->push(Carbon::parse($date)->format('M d'));
            
            $dayRevenue = Sale::where('location_id', $locationId)
                ->whereDate('created_at', $date)
                ->sum('net_amount');
            $revenueData->push($dayRevenue);
            
            $dayExpense = Expense::where('location_id', $locationId)
                ->whereDate('created_at', $date)
                ->sum('amount');
            $expenseData->push($dayExpense);
            
            $dayGrossProfit = SaleItem::whereHas('sale', function ($q) use ($locationId, $date) {
                    $q->where('location_id', $locationId)
                      ->whereDate('created_at', $date);
                })
                ->sum(DB::raw('(unit_price - cost_price) * quantity'));
                
            $profitData->push($dayGrossProfit - $dayExpense);
        }

        $bestSellingProducts = SaleItem::selectRaw('product_id, SUM(quantity) as total_quantity, AVG(unit_price) as avg_price, SUM(subtotal) as total_sales_amount')
            ->whereHas('sale', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->with('product')
            ->get();

        $bestCustomers = Sale::selectRaw('customer_id, SUM(net_amount) as total_spent, SUM(net_amount - paid_amount) as total_dues')
            ->whereNotNull('customer_id')
            ->where('location_id', $locationId)
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->take(5)
            ->with('customer')
            ->get();

        return view('dashboard', compact(
            'thisMonthPurchases',
            'totalPurchases',
            'thisMonthSales',
            'totalSales',
            'customerBalance',
            'vendorBalance',
            'totalStockValue',
            'selfBalance',
            'salesCountThisMonth',
            'expensesThisMonth',
            'grossProfitThisMonth',
            'netProfitThisMonth',
            'revenueDates',
            'revenueData',
            'expenseData',
            'profitData',
            'bestSellingProducts',
            'bestCustomers'
        ));
    }
}
