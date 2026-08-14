<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\PaymentReceive;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        $from = $request->input('from_date', date('Y-m-01'));
        $to = $request->input('to_date', date('Y-m-d'));

        $salesQuery = Sale::query();
        $expensesQuery = Expense::query();

        if ($from) {
            $salesQuery->whereDate('transaction_date', '>=', $from);
            $expensesQuery->whereDate('date', '>=', $from);
        }
        if ($to) {
            $salesQuery->whereDate('transaction_date', '<=', $to);
            $expensesQuery->whereDate('date', '<=', $to);
        }

        $sales = $salesQuery->get();
        $saleIds = $sales->pluck('id');

        // Fetch product-wise sales
        $productSales = SaleItem::whereIn('sale_id', $saleIds)
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_sales_revenue'),
                DB::raw('SUM(quantity * cost_price) as total_cogs')
            )
            ->groupBy('product_id')
            ->with('product')
            ->get();

        $items = [];
        $totalProductProfit = 0;

        foreach ($productSales as $ps) {
            $qty = $ps->total_qty;
            if ($qty <= 0) continue;

            $avgPurchase = $ps->total_cogs / $qty;
            $avgSale = $ps->total_sales_revenue / $qty;
            $profitUnit = $avgSale - $avgPurchase;
            $profit = $ps->total_sales_revenue - $ps->total_cogs;

            $totalProductProfit += $profit;

            $items[] = [
                'product' => $ps->product?->name ?? 'N/A',
                'sku' => $ps->product?->sku ?? '-',
                'avg_purchase_price' => $avgPurchase,
                'avg_sale_price' => $avgSale,
                'sold_qty' => $qty,
                'profit_per_unit' => $profitUnit,
                'profit' => $profit
            ];
        }

        $totalExpenses = $expensesQuery->sum('amount');
        $totalDiscounts = Sale::whereIn('id', $saleIds)->sum('discount');
        $netProfit = $totalProductProfit - $totalExpenses - $totalDiscounts;

        return view('reports.profit_loss', compact(
            'items', 
            'totalProductProfit', 
            'totalExpenses', 
            'totalDiscounts', 
            'netProfit', 
            'from', 
            'to'
        ));
    }

    public function dailyCashBook(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        // 1. Calculate Opening Balance
        $openingBalance = $this->getBalanceBeforeDate($date);

        $credits = [];
        $debits = [];

        // Deposits/Withdrawals
        $trans = Transaction::whereDate('transaction_date', $date)->with('account')->get();
        foreach ($trans as $t) {
            $typeFormatted = ucfirst($t->type);
            if ($t->type == 'deposit') {
                $credits[] = [
                    'ref' => $t->reference_no,
                    'account' => $t->account->name ?? 'N/A',
                    'note' => $typeFormatted . ' - Ref: ' . $t->reference_no . ($t->note ? ' (' . $t->note . ')' : ''),
                    'amount' => $t->amount
                ];
            } else if ($t->type == 'withdrawal') {
                $debits[] = [
                    'ref' => $t->reference_no,
                    'account' => $t->account->name ?? 'N/A',
                    'note' => $typeFormatted . ' - Ref: ' . $t->reference_no . ($t->note ? ' (' . $t->note . ')' : ''),
                    'amount' => $t->amount
                ];
            }
        }

        // Sales (paid_amount > 0)
        $sales = Sale::whereDate('created_at', $date)->with(['customer', 'account'])->get();
        foreach ($sales as $s) {
            if ($s->paid_amount > 0) {
                $pending = $s->net_amount - $s->paid_amount;
                $paymentInfo = ', Received: ' . $s->paid_amount . ($s->payment_method ? ' ' . $s->payment_method : '') . ', Pending: ' . $pending;
                $credits[] = [
                    'ref' => 'SALE-' . $s->id,
                    'account' => $s->account?->name ?? 'Cash',
                    'note' => 'Sale - INV-' . $s->id . $paymentInfo,
                    'amount' => $s->paid_amount
                ];
            }
        }

        // Purchases (paid_amount > 0)
        $purchases = Purchase::whereDate('created_at', $date)->with(['vendor', 'account'])->get();
        foreach ($purchases as $p) {
            if ($p->paid_amount > 0) {
                $pending = $p->net_amount - $p->paid_amount;
                $paymentInfo = ', Paid: ' . $p->paid_amount . ', Pending: ' . $pending;
                $debits[] = [
                    'ref' => 'PUR-' . $p->id,
                    'account' => $p->account?->name ?? 'Cash/Bank',
                    'note' => 'Purchase - PUR-' . $p->id . $paymentInfo,
                    'amount' => $p->paid_amount
                ];
            }
        }

        // Payment Receives
        $receives = PaymentReceive::whereDate('date', $date)->with(['account', 'fromAccount'])->get();
        foreach ($receives as $r) {
            $fromType = $r->fromAccount ? $r->fromAccount->type : '';
            if ($fromType === 'vendor') {
                $debits[] = [
                    'ref' => $r->reference_no,
                    'account' => $r->account->name ?? 'N/A',
                    'note' => 'Payment Made - Ref: ' . $r->reference_no . ' to ' . ($r->fromAccount ? $r->fromAccount->name : 'Business') . ($r->note ? ' (' . $r->note . ')' : ''),
                    'amount' => $r->amount
                ];
            } else {
                $credits[] = [
                    'ref' => $r->reference_no,
                    'account' => $r->account->name ?? 'N/A',
                    'note' => 'Payment Received - Ref: ' . $r->reference_no . ' from ' . ($r->fromAccount ? $r->fromAccount->name : 'Customer/Vendor') . ($r->note ? ' (' . $r->note . ')' : ''),
                    'amount' => $r->amount
                ];
            }
        }

        // Transfers
        $transfers = Transfer::whereDate('date', $date)->with(['fromAccount', 'toAccount'])->get();
        foreach ($transfers as $tr) {
            $fromAcc = $tr->fromAccount;
            $toAcc = $tr->toAccount;
            
            if ($fromAcc && $fromAcc->type == 'business') {
                $debits[] = [
                    'ref' => $tr->reference_no,
                    'account' => $fromAcc->name ?? 'N/A',
                    'note' => 'Transferred from ' . ($fromAcc ? $fromAcc->name : 'Unknown') . ' to ' . ($toAcc ? $toAcc->name : 'Unknown') . ($tr->note ? ' - ' . $tr->note : ''),
                    'amount' => $tr->amount
                ];
            }
            if ($toAcc && $toAcc->type == 'business') {
                $credits[] = [
                    'ref' => $tr->reference_no,
                    'account' => $toAcc->name ?? 'N/A',
                    'note' => 'Transferred from ' . ($fromAcc ? $fromAcc->name : 'Unknown') . ' to ' . ($toAcc ? $toAcc->name : 'Unknown') . ($tr->note ? ' - ' . $tr->note : ''),
                    'amount' => $tr->amount
                ];
            }
        }

        // Expenses
        $expenses = Expense::whereDate('date', $date)->with(['account', 'category'])->get();
        foreach ($expenses as $ex) {
            $debits[] = [
                'ref' => $ex->reference_no,
                'account' => $ex->account->name ?? 'N/A',
                'note' => 'Expense: ' . ($ex->category->name ?? 'General') . ' - Ref: ' . $ex->reference_no . ($ex->note ? ' (' . $ex->note . ')' : ''),
                'amount' => $ex->amount
            ];
        }

        $totalCredits = collect($credits)->sum('amount');
        $totalDebits = collect($debits)->sum('amount');
        $closingBalance = $openingBalance + $totalCredits - $totalDebits;

        return view('reports.daily_cash_book', compact(
            'credits',
            'debits',
            'openingBalance',
            'totalCredits',
            'totalDebits',
            'closingBalance',
            'date'
        ));
    }

    private function getBalanceBeforeDate($date)
    {
        // 1. Initial balances of all business accounts
        $initialBalance = Account::business()->sum('balance');

        // 2. Historical Deposits & Withdrawals before $date
        $inflowTrans = Transaction::where('transaction_date', '<', $date . ' 00:00:00')->where('type', 'deposit')->sum('amount');
        $outflowTrans = Transaction::where('transaction_date', '<', $date . ' 00:00:00')->where('type', 'withdrawal')->sum('amount');

        // 3. Historical Sales paid_amount before $date
        $inflowSales = Sale::where('transaction_date', '<', $date . ' 00:00:00')->sum('paid_amount');

        // 4. Historical Purchases paid_amount before $date
        $outflowPurchases = Purchase::where('transaction_date', '<', $date . ' 00:00:00')->sum('paid_amount');

        // 5. Historical Payment Receives before $date
        $inflowReceives = PaymentReceive::join('accounts as from_acc', 'payment_receives.from_account_id', '=', 'from_acc.id')
            ->where('payment_receives.date', '<', $date . ' 00:00:00')
            ->where('from_acc.type', '!=', 'vendor')
            ->sum('payment_receives.amount');

        $outflowReceives = PaymentReceive::join('accounts as from_acc', 'payment_receives.from_account_id', '=', 'from_acc.id')
            ->where('payment_receives.date', '<', $date . ' 00:00:00')
            ->where('from_acc.type', 'vendor')
            ->sum('payment_receives.amount');

        // 6. Historical Transfers before $date
        $inflowTransfers = Transfer::join('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
            ->where('transfers.date', '<', $date . ' 00:00:00')
            ->where('to_acc.type', 'business')
            ->sum('transfers.amount');

        $outflowTransfers = Transfer::join('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
            ->where('transfers.date', '<', $date . ' 00:00:00')
            ->where('from_acc.type', 'business')
            ->sum('transfers.amount');

        // 7. Historical Expenses before $date
        $outflowExpenses = Expense::where('date', '<', $date . ' 00:00:00')->sum('amount');

        $totalInflow = $inflowTrans + $inflowSales + $inflowReceives + $inflowTransfers;
        $totalOutflow = $outflowTrans + $outflowPurchases + $outflowTransfers + $outflowExpenses + $outflowReceives;

        return $initialBalance + $totalInflow - $totalOutflow;
    }

    public function productWiseSales(Request $request)
    {
        $from = $request->input('from_date', date('Y-m-01'));
        $to = $request->input('to_date', date('Y-m-d'));
        $selectedProductId = $request->input('product_id');

        $query = SaleItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->groupBy('product_id')
            ->with('product.category');

        if ($selectedProductId) {
            $query->where('product_id', $selectedProductId);
        }

        if ($from || $to) {
            $query->whereHas('sale', function($q) use ($from, $to) {
                if ($from) $q->where('transaction_date', '>=', $from . ' 00:00:00');
                if ($to) $q->where('transaction_date', '<=', $to . ' 23:59:59');
            });
        }

        $sales = $query->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('reports.product_wise_sales', compact('sales', 'products', 'selectedProductId', 'from', 'to'));
    }

    public function ledgerReport(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $accountKey = $request->input('account');
        
        $type = 'all';
        $id = null;
        $selectedName = 'All Accounts';
        
        if ($accountKey) {
            list($type, $id) = explode('_', $accountKey);
            $account = Account::find($id);
            if ($account) {
                if ($type === 'business') $selectedName = $account->name . ' (Business Account)';
                if ($type === 'customer') $selectedName = $account->name . ' (Customer)';
                if ($type === 'vendor') $selectedName = $account->name . ' (Vendor)';
            }
        }

        $ledgerService = new \App\Services\LedgerService();

        if ($request->ajax()) {
            $unionQuery = $ledgerService->getLedgerQuery($type, $id, $from, $to);
            $baseSql = "({$unionQuery->toSql()}) as combined";
            
            // Search
            $searchValue = $request->input('search.value');
            $query = DB::table(DB::raw($baseSql))->mergeBindings($unionQuery);
            
            if ($searchValue) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('account', 'like', "%{$searchValue}%")
                      ->orWhere('description', 'like', "%{$searchValue}%");
                });
            }

            $totalRecords = DB::table(DB::raw($baseSql))->mergeBindings($unionQuery)->count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 50);

            // Get offset sum for running balance
            $offsetBalance = 0;
            if ($start > 0) {
                $offsetQuery = DB::table(DB::raw($baseSql))->mergeBindings($unionQuery);
                if ($searchValue) {
                    $offsetQuery->where(function($q) use ($searchValue) {
                        $q->where('account', 'like', "%{$searchValue}%")
                          ->orWhere('description', 'like', "%{$searchValue}%");
                    });
                }
                $offsetTotals = $offsetQuery->orderBy('date', 'asc')->limit($start)
                    ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')->first();
                $offsetBalance = ($offsetTotals->total_debit ?? 0) - ($offsetTotals->total_credit ?? 0);
            }

            $rows = $query->orderBy('date', 'asc')->offset($start)->limit($length)->get();

            // Starting running balance is Opening Balance + Offset Balance
            $runningBalance = $ledgerService->getOpeningBalance($type, $id, $from) + $offsetBalance;

            $data = [];
            foreach ($rows as $row) {
                $runningBalance += ($row->debit - $row->credit);
                $data[] = [
                    'date' => $row->date,
                    'account' => $row->account,
                    'description' => $row->description,
                    'debit' => $row->debit,
                    'credit' => $row->credit,
                    'balance' => $runningBalance
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'openingBalance' => $ledgerService->getOpeningBalance($type, $id, $from)
            ]);
        }

        $accounts = Account::business()->get();
        $customers = Account::customers()->get();
        $vendors = Account::vendors()->get();

        $openingBalance = 0;
        $closingBalance = 0;

        return view('reports.ledger_report', compact(
            'accounts', 'customers', 'vendors', 
            'from', 'to', 'accountKey', 'selectedName',
            'openingBalance', 'closingBalance'
        ));
    }

    public function stockReport(Request $request)
    {
        $products = Product::with(['category', 'stocks'])->get();
        return view('reports.stock_report', compact('products'));
    }

    public function customerWiseSales(Request $request)
    {
        $from = $request->input('from_date', date('Y-m-01'));
        $to = $request->input('to_date', date('Y-m-d'));
        $selectedCustomerId = $request->input('customer_id');

        $customersList = Account::customers()->orderBy('name')->get();
        $customers = [];
        $salesData = [];

        if ($selectedCustomerId) {
            $query = SaleItem::query()
                ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
                ->whereHas('sale', function($q) use ($selectedCustomerId, $from, $to) {
                    $q->where('customer_id', $selectedCustomerId);
                    if ($from) $q->where('transaction_date', '>=', $from . ' 00:00:00');
                    if ($to) $q->where('transaction_date', '<=', $to . ' 23:59:59');
                })
                ->groupBy('product_id')
                ->with('product');

            $salesData = $query->get();
        } else {
            $customers = Account::customers()->with(['sales' => function($q) use ($from, $to) {
                if ($from) $q->where('transaction_date', '>=', $from . ' 00:00:00');
                if ($to) $q->where('transaction_date', '<=', $to . ' 23:59:59');
            }])->get();
        }

        return view('reports.customer_wise_sales', compact(
            'customersList', 'selectedCustomerId', 'from', 'to', 'salesData', 'customers'
        ));
    }
}
