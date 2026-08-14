<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Account;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['category', 'account'])->orderBy('id', 'desc')->get();
        $categories = ExpenseCategory::all();
        // Only business accounts
        $accounts = Account::business()->get();
        
        return view('expenses.index', compact('expenses', 'categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        // Deduct from business account balance
        $account = Account::findOrFail($request->account_id);
        $account->balance -= $request->amount;
        $account->save();

        // Generate Ref#
        $latest = Expense::orderBy('id', 'desc')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $refNo = 'EXP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        Expense::create([
            'reference_no' => $refNo,
            'expense_category_id' => $request->expense_category_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'note' => $request->note,
            'location_id' => auth()->user()->location_id ?? null,
            'user_id' => auth()->id() ?? null
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Refund back to business account balance
        $account = Account::findOrFail($expense->account_id);
        $account->balance += $expense->amount;
        $account->save();

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
