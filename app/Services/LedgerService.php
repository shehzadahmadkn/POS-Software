<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Account;

class LedgerService
{
    public function getLedgerQuery($type, $id, $from, $to)
    {
        $queries = [];

        if ($type === 'business') {
            $queries[] = DB::table('transactions')
                ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->selectRaw("transaction_date as date, accounts.name as account, CONCAT(UCASE(SUBSTRING(transactions.type, 1, 1)), SUBSTRING(transactions.type, 2), ' - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, IF(transactions.type='deposit', amount, 0) as debit, IF(transactions.type='withdrawal', amount, 0) as credit")
                ->where('account_id', $id);

            $queries[] = DB::table('payment_receives')
                ->join('accounts', 'payment_receives.account_id', '=', 'accounts.id')
                ->leftJoin('accounts as from_acc', 'payment_receives.from_account_id', '=', 'from_acc.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Payment Received - Ref: ', IFNULL(reference_no,''), ' from ', IFNULL(from_acc.name, 'Customer/Vendor'), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit")
                ->where('payment_receives.account_id', $id);

            $queries[] = DB::table('transfers')
                ->join('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
                ->leftJoin('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
                ->selectRaw("date, from_acc.name as account, CONCAT('Transferred from ', IFNULL(from_acc.name, 'Unknown'), ' to ', IFNULL(to_acc.name, 'Unknown'), IF(note IS NOT NULL AND note != '', CONCAT(' - ', note), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $id);

            $queries[] = DB::table('transfers')
                ->join('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
                ->leftJoin('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
                ->selectRaw("date, to_acc.name as account, CONCAT('Transferred from ', IFNULL(from_acc.name, 'Unknown'), ' to ', IFNULL(to_acc.name, 'Unknown'), IF(note IS NOT NULL AND note != '', CONCAT(' - ', note), '')) as description, amount as debit, 0 as credit")
                ->where('to_account_id', $id);

            $queries[] = DB::table('expenses')
                ->join('accounts', 'expenses.account_id', '=', 'accounts.id')
                ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Expense: ', IFNULL(expense_categories.name, 'General'), ' - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('expenses.account_id', $id);

        } elseif ($type === 'customer') {
            $queries[] = DB::table('sales')
                ->join('accounts', 'sales.customer_id', '=', 'accounts.id')
                ->selectRaw("DATE(sales.created_at) as date, accounts.name as account, CONCAT('Sale - INV-', sales.id, IF(paid_amount > 0, CONCAT(', Received: ', paid_amount, IFNULL(CONCAT(' ', payment_method), ''), ', Pending: ', (net_amount - paid_amount)), '')) as description, net_amount as debit, IF(paid_amount > 0, paid_amount, 0) as credit")
                ->where('customer_id', $id);

            $queries[] = DB::table('payment_receives')
                ->join('accounts', 'payment_receives.from_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Payment Refunded/Made - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $id);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.from_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer Out - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $id);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.to_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer In - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit")
                ->where('to_account_id', $id);

        } elseif ($type === 'vendor') {
            $queries[] = DB::table('purchases')
                ->join('accounts', 'purchases.vendor_id', '=', 'accounts.id')
                ->selectRaw("DATE(purchases.created_at) as date, accounts.name as account, CONCAT('Purchase - PUR-', purchases.id, IF(paid_amount > 0, CONCAT(', Paid: ', paid_amount, ', Pending: ', (net_amount - paid_amount)), '')) as description, IF(paid_amount > 0, paid_amount, 0) as debit, total_amount as credit")
                ->where('vendor_id', $id);

            $queries[] = DB::table('payment_receives')
                ->join('accounts', 'payment_receives.from_account_id', '=', 'accounts.id')
                ->leftJoin('accounts as to_acc', 'payment_receives.account_id', '=', 'to_acc.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Payment Made - Ref: ', IFNULL(reference_no,''), ' to ', IFNULL(to_acc.name, 'Business'), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $id);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.from_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer Out - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $id);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.to_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer In - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit")
                ->where('to_account_id', $id);

        } elseif ($type === 'all') {
            // For All Accounts, we combine everything for business accounts (which is what the old logic did)
            $queries[] = DB::table('transactions')
                ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->selectRaw("transaction_date as date, accounts.name as account, CONCAT(UCASE(SUBSTRING(transactions.type, 1, 1)), SUBSTRING(transactions.type, 2), ' - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, IF(transactions.type='deposit', amount, 0) as debit, IF(transactions.type='withdrawal', amount, 0) as credit");

            $queries[] = DB::table('payment_receives')
                ->join('accounts', 'payment_receives.account_id', '=', 'accounts.id')
                ->leftJoin('accounts as from_acc', 'payment_receives.from_account_id', '=', 'from_acc.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Payment Received - Ref: ', IFNULL(reference_no,''), ' from ', IFNULL(from_acc.name, 'Customer/Vendor'), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit");

            $queries[] = DB::table('transfers')
                ->join('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
                ->leftJoin('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
                ->selectRaw("date, from_acc.name as account, CONCAT('Transferred from ', IFNULL(from_acc.name, 'Unknown'), ' to ', IFNULL(to_acc.name, 'Unknown'), IF(note IS NOT NULL AND note != '', CONCAT(' - ', note), '')) as description, 0 as debit, amount as credit");

            $queries[] = DB::table('transfers')
                ->join('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
                ->leftJoin('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
                ->selectRaw("date, to_acc.name as account, CONCAT('Transferred from ', IFNULL(from_acc.name, 'Unknown'), ' to ', IFNULL(to_acc.name, 'Unknown'), IF(note IS NOT NULL AND note != '', CONCAT(' - ', note), '')) as description, amount as debit, 0 as credit");

            $queries[] = DB::table('expenses')
                ->join('accounts', 'expenses.account_id', '=', 'accounts.id')
                ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Expense: ', IFNULL(expense_categories.name, 'General'), ' - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit");
        } elseif ($type === 'group') {
            $group = DB::table('group_accounts')->find($id);
            $customerId = $group->customer_id ?? 0;
            $vendorId = $group->vendor_id ?? 0;

            // Customer Queries
            $queries[] = DB::table('sales')
                ->join('accounts', 'sales.customer_id', '=', 'accounts.id')
                ->selectRaw("DATE(sales.created_at) as date, accounts.name as account, CONCAT('Sale - INV-', sales.id, IF(paid_amount > 0, CONCAT(', Received: ', paid_amount, IFNULL(CONCAT(' ', payment_method), ''), ', Pending: ', (net_amount - paid_amount)), '')) as description, net_amount as debit, IF(paid_amount > 0, paid_amount, 0) as credit")
                ->where('customer_id', $customerId);

            $queries[] = DB::table('payment_receives')
                ->join('accounts', 'payment_receives.from_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Payment Refunded/Made - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $customerId);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.from_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer Out - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $customerId);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.to_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer In - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit")
                ->where('to_account_id', $customerId);

            // Vendor Queries
            $queries[] = DB::table('purchases')
                ->join('accounts', 'purchases.vendor_id', '=', 'accounts.id')
                ->selectRaw("DATE(purchases.created_at) as date, accounts.name as account, CONCAT('Purchase - PUR-', purchases.id, IF(paid_amount > 0, CONCAT(', Paid: ', paid_amount, ', Pending: ', (net_amount - paid_amount)), '')) as description, IF(paid_amount > 0, paid_amount, 0) as debit, total_amount as credit")
                ->where('vendor_id', $vendorId);

            $queries[] = DB::table('payment_receives')
                ->join('accounts', 'payment_receives.from_account_id', '=', 'accounts.id')
                ->leftJoin('accounts as to_acc', 'payment_receives.account_id', '=', 'to_acc.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Payment Made - Ref: ', IFNULL(reference_no,''), ' to ', IFNULL(to_acc.name, 'Business'), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $vendorId);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.from_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer Out - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
                ->where('from_account_id', $vendorId);

            $queries[] = DB::table('transfers')
                ->join('accounts', 'transfers.to_account_id', '=', 'accounts.id')
                ->selectRaw("date, accounts.name as account, CONCAT('Transfer In - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit")
                ->where('to_account_id', $vendorId);
        }

        // Apply date filters to all queries
        foreach ($queries as $k => $query) {
            $sql = $query->toSql();
            $dateCol = 'date'; // default for transfers, expenses, payment_receives
            if (str_contains($sql, '`transactions`')) {
                $dateCol = 'transactions.transaction_date';
            } elseif (str_contains($sql, '`sales`')) {
                $dateCol = 'sales.created_at';
            } elseif (str_contains($sql, '`purchases`')) {
                $dateCol = 'purchases.created_at';
            }

            if ($from) {
                $queries[$k]->where($dateCol, '>=', $from . ' 00:00:00');
            }
            if ($to) {
                $queries[$k]->where($dateCol, '<=', $to . ' 23:59:59');
            }
        }

        $unionQuery = array_shift($queries);
        foreach ($queries as $q) {
            $unionQuery = $unionQuery->unionAll($q);
        }

        return $unionQuery;
    }

    public function getOpeningBalance($type, $id, $from)
    {
        if (!$from) return 0;

        if ($type === 'business') {
            $priorDebit = DB::table('transactions')->where('account_id', $id)->where('transaction_date', '<', $from . ' 00:00:00')->where('type', 'deposit')->sum('amount')
                + DB::table('payment_receives')->where('account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('transfers')->where('to_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            $priorCredit = DB::table('transactions')->where('account_id', $id)->where('transaction_date', '<', $from . ' 00:00:00')->where('type', 'withdrawal')->sum('amount')
                + DB::table('transfers')->where('from_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('expenses')->where('account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            return $priorDebit - $priorCredit;

        } elseif ($type === 'customer') {
            $priorDebit = DB::table('sales')->where('customer_id', $id)->where('created_at', '<', $from . ' 00:00:00')->sum('net_amount')
                + DB::table('transfers')->where('to_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            $priorCredit = DB::table('payment_receives')->where('from_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('sales')->where('customer_id', $id)->where('created_at', '<', $from . ' 00:00:00')->sum('paid_amount')
                + DB::table('transfers')->where('from_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            return $priorDebit - $priorCredit;

        } elseif ($type === 'vendor') {
            $priorCredit = DB::table('purchases')->where('vendor_id', $id)->where('created_at', '<', $from . ' 00:00:00')->sum('total_amount')
                + DB::table('payment_receives')->where('from_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('transfers')->where('from_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            $priorDebit = DB::table('purchases')->where('vendor_id', $id)->where('created_at', '<', $from . ' 00:00:00')->sum('paid_amount')
                + DB::table('transfers')->where('to_account_id', $id)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            return $priorCredit - $priorDebit;
            
        } elseif ($type === 'all') {
            $priorDebit = DB::table('transactions')->where('transaction_date', '<', $from . ' 00:00:00')->where('type', 'deposit')->sum('amount')
                + DB::table('payment_receives')->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('transfers')->join('accounts', 'transfers.to_account_id', '=', 'accounts.id')->where('accounts.type', 'business')->where('transfers.date', '<', $from . ' 00:00:00')->sum('transfers.amount');
            $priorCredit = DB::table('transactions')->where('transaction_date', '<', $from . ' 00:00:00')->where('type', 'withdrawal')->sum('amount')
                + DB::table('transfers')->join('accounts', 'transfers.from_account_id', '=', 'accounts.id')->where('accounts.type', 'business')->where('transfers.date', '<', $from . ' 00:00:00')->sum('transfers.amount')
                + DB::table('expenses')->where('date', '<', $from . ' 00:00:00')->sum('amount');
            return $priorDebit - $priorCredit;
        } elseif ($type === 'group') {
            $group = DB::table('group_accounts')->find($id);
            $customerId = $group->customer_id ?? 0;
            $vendorId = $group->vendor_id ?? 0;

            // Customer
            $custPriorDebit = DB::table('sales')->where('customer_id', $customerId)->where('created_at', '<', $from . ' 00:00:00')->sum('net_amount')
                + DB::table('transfers')->where('to_account_id', $customerId)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            $custPriorCredit = DB::table('payment_receives')->where('from_account_id', $customerId)->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('sales')->where('customer_id', $customerId)->where('created_at', '<', $from . ' 00:00:00')->sum('paid_amount')
                + DB::table('transfers')->where('from_account_id', $customerId)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            
            // Vendor
            $venPriorCredit = DB::table('purchases')->where('vendor_id', $vendorId)->where('created_at', '<', $from . ' 00:00:00')->sum('total_amount')
                + DB::table('payment_receives')->where('from_account_id', $vendorId)->where('date', '<', $from . ' 00:00:00')->sum('amount')
                + DB::table('transfers')->where('from_account_id', $vendorId)->where('date', '<', $from . ' 00:00:00')->sum('amount');
            $venPriorDebit = DB::table('purchases')->where('vendor_id', $vendorId)->where('created_at', '<', $from . ' 00:00:00')->sum('paid_amount')
                + DB::table('transfers')->where('to_account_id', $vendorId)->where('date', '<', $from . ' 00:00:00')->sum('amount');

            $groupPriorDebit = $custPriorDebit + $venPriorDebit;
            $groupPriorCredit = $custPriorCredit + $venPriorCredit;
            
            return $groupPriorDebit - $groupPriorCredit;
        }

        return 0;
    }
}
