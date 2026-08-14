<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$id = 1;
$from = '2020-01-01';
$to = '2030-01-01';

// Let's build the Business Account query
$q1 = DB::table('transactions')
    ->selectRaw("transaction_date as date, CONCAT(UCASE(SUBSTRING(type, 1, 1)), SUBSTRING(type, 2), ' - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, IF(type='deposit', amount, 0) as debit, IF(type='withdrawal', amount, 0) as credit")
    ->where('account_id', $id)
    ->whereDate('transaction_date', '>=', $from)
    ->whereDate('transaction_date', '<=', $to);

$q2 = DB::table('payment_receives')
    ->leftJoin('accounts', 'payment_receives.from_account_id', '=', 'accounts.id')
    ->selectRaw("date, CONCAT('Payment Received - Ref: ', IFNULL(reference_no,''), ' from ', IFNULL(accounts.name, 'Customer/Vendor'), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, amount as debit, 0 as credit")
    ->where('payment_receives.account_id', $id)
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to);

$q3 = DB::table('transfers')
    ->leftJoin('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
    ->leftJoin('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
    ->selectRaw("date, CONCAT('Transferred from ', IFNULL(from_acc.name, 'Unknown'), ' to ', IFNULL(to_acc.name, 'Unknown'), IF(note IS NOT NULL AND note != '', CONCAT(' - ', note), '')) as description, 0 as debit, amount as credit")
    ->where('from_account_id', $id)
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to);

$q4 = DB::table('transfers')
    ->leftJoin('accounts as from_acc', 'transfers.from_account_id', '=', 'from_acc.id')
    ->leftJoin('accounts as to_acc', 'transfers.to_account_id', '=', 'to_acc.id')
    ->selectRaw("date, CONCAT('Transferred from ', IFNULL(from_acc.name, 'Unknown'), ' to ', IFNULL(to_acc.name, 'Unknown'), IF(note IS NOT NULL AND note != '', CONCAT(' - ', note), '')) as description, amount as debit, 0 as credit")
    ->where('to_account_id', $id)
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to);

$q5 = DB::table('expenses')
    ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
    ->selectRaw("date, CONCAT('Expense: ', IFNULL(expense_categories.name, 'General'), ' - Ref: ', IFNULL(reference_no,''), IF(note IS NOT NULL AND note != '', CONCAT(' (', note, ')'), '')) as description, 0 as debit, amount as credit")
    ->where('account_id', $id)
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to);

$unionQuery = $q1->unionAll($q2)->unionAll($q3)->unionAll($q4)->unionAll($q5);

$results = DB::table(DB::raw("({$unionQuery->toSql()}) as combined"))
    ->mergeBindings($unionQuery)
    ->orderBy('date', 'asc')
    ->limit(5)
    ->get();

print_r($results);
