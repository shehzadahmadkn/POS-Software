<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('account')->orderBy('id', 'desc')->get();
        $accounts = Account::business()->get();
        $customers = Account::customers()->get();
        $vendors = Account::vendors()->get();
        
        return view('transactions.index', compact('transactions', 'accounts', 'customers', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        $account = Account::findOrFail($request->account_id);

        if ($request->type === 'deposit') {
            $account->balance += $request->amount;
        } else {
            $account->balance -= $request->amount;
        }
        $account->save();

        $latest = Transaction::orderBy('id', 'desc')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $refNo = 'TRX-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Transaction::create([
            'reference_no' => $refNo,
            'account_id' => $request->account_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'note' => $request->note
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $account = Account::findOrFail($transaction->account_id);

        // Reverse the effect
        if ($transaction->type === 'deposit') {
            $account->balance -= $transaction->amount;
        } else {
            $account->balance += $transaction->amount;
        }
        $account->save();

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
