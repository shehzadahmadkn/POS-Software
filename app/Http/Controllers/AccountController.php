<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::business()->orderBy('id', 'desc')->get();
        return view('accounts.index', compact('accounts'));
    }

    public function statement(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $from = $request->input('from');
        $to = $request->input('to');

        $ledgerService = new \App\Services\LedgerService();
        $unionQuery = $ledgerService->getLedgerQuery($type, $id, $from, $to);
        $baseSql = "({$unionQuery->toSql()}) as combined";
        
        $query = \Illuminate\Support\Facades\DB::table(\Illuminate\Support\Facades\DB::raw($baseSql))
            ->mergeBindings($unionQuery);
            
        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('description', 'like', "%{$searchValue}%");
            });
        }

        // DataTables pagination parameters
        $start = $request->input('start', 0);
        $length = $request->input('length', 100);

        $totalRecords = \Illuminate\Support\Facades\DB::table(\Illuminate\Support\Facades\DB::raw($baseSql))
            ->mergeBindings($unionQuery)
            ->count();
        $filteredRecords = $query->count();

        // Get offset sum for running balance
        $offsetBalance = 0;
        if ($start > 0) {
            $offsetQuery = \Illuminate\Support\Facades\DB::table(\Illuminate\Support\Facades\DB::raw($baseSql))->mergeBindings($unionQuery);
            if ($searchValue) {
                $offsetQuery->where(function($q) use ($searchValue) {
                    $q->where('description', 'like', "%{$searchValue}%");
                });
            }
            $offsetTotals = $offsetQuery->orderBy('date', 'asc')->limit($start)
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')->first();
            $offsetBalance = ($offsetTotals->total_debit ?? 0) - ($offsetTotals->total_credit ?? 0);
        }

        $rows = $query->orderBy('date', 'asc')->offset($start)->limit($length)->get();
        $runningBalance = $ledgerService->getOpeningBalance($type, $id, $from) + $offsetBalance;

        $data = [];
        foreach ($rows as $row) {
            $runningBalance += ($row->debit - $row->credit);
            $data[] = [
                'date' => $row->date,
                'description' => $row->description,
                'debit' => $row->debit,
                'credit' => $row->credit,
                'balance' => $runningBalance
            ];
        }

        return response()->json([
            'success' => true,
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
            'openingBalance' => $ledgerService->getOpeningBalance($type, $id, $from)
        ]);
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Business,Customer,Vendor',
            'initial_amount' => 'nullable|numeric',
        ]);

        $initialAmount = $request->initial_amount ?? 0;

        if ($request->type === 'Business') {
            $request->validate([
                'category' => 'required|in:cash,bank,cheque',
                'balance_type' => 'required|in:credit,debit',
            ]);

            $balance = $request->balance_type === 'debit' ? abs($initialAmount) : -abs($initialAmount);

            Account::create([
                'name' => $request->name,
                'type' => 'business',
                'sub_type' => $request->category,
                'balance' => $balance
            ]);
            
            $msg = 'Business account created successfully!';
        } elseif ($request->type === 'Customer') {
            $request->validate([
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'balance_type_cv' => 'required|in:credit,debit',
            ]);

            $balance = $request->balance_type_cv === 'debit' ? abs($initialAmount) : -abs($initialAmount);

            Account::create([
                'name' => $request->name,
                'type' => 'customer',
                'phone' => $request->phone,
                'address' => $request->address,
                'balance' => $balance
            ]);

            $msg = 'Customer created successfully!';
        } elseif ($request->type === 'Vendor') {
            $request->validate([
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'balance_type_cv' => 'required|in:credit,debit',
            ]);

            $balance = $request->balance_type_cv === 'debit' ? abs($initialAmount) : -abs($initialAmount);

            Account::create([
                'name' => $request->name,
                'type' => 'vendor',
                'phone' => $request->phone,
                'address' => $request->address,
                'balance' => $balance
            ]);

            $msg = 'Vendor created successfully!';
        }

        return redirect()->back()->with('success', $msg);
    }
}
