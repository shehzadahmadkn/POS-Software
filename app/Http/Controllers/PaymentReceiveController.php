<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceive;
use App\Models\Account;

use Illuminate\Http\Request;

class PaymentReceiveController extends Controller
{
    public function index()
    {
        $payments = PaymentReceive::with('account')->orderBy('id', 'desc')->get();
        // Only get business accounts
        $accounts = Account::business()->get();
        $customers = Account::customers()->get();
        $vendors = Account::vendors()->get();
        
        return view('payment_receives.index', compact('payments', 'accounts', 'customers', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from' => 'required|string', // Format: type_id (e.g., customer_1)
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        list($from_type, $from_id) = explode('_', $request->from);

        $account = Account::findOrFail($request->account_id);
        
        // Add to business account
        $account->balance += $request->amount;
        $account->save();

        // Reduce from customer/vendor
        if ($from_type === 'customer') {
            $entity = Account::customers()->findOrFail($from_id);
            $entity->balance -= $request->amount;
            $entity->save();
        } else {
            $entity = Account::vendors()->findOrFail($from_id);
            $entity->balance += $request->amount;
            $entity->save();
        }

        // Generate Ref: PR-001
        $latest = PaymentReceive::orderBy('id', 'desc')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $refNo = 'PR-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        PaymentReceive::create([
            'reference_no' => $refNo,
            'from_account_id' => $from_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'note' => $request->note
        ]);

        return redirect()->route('payment_receives.index')->with('success', 'Payment received successfully.');
    }

    public function destroy($id)
    {
        $payment = PaymentReceive::findOrFail($id);
        $account = Account::findOrFail($payment->account_id);

        // Reverse the business account effect
        $account->balance -= $payment->amount;
        $account->save();

        // Reverse the customer/vendor effect
        if ($payment->from->type === 'customer') {
            $entity = Account::customers()->findOrFail($payment->from_account_id);
            $entity->balance += $payment->amount;
            $entity->save();
        } else {
            $entity = Account::vendors()->findOrFail($payment->from_account_id);
            $entity->balance -= $payment->amount;
            $entity->save();
        }

        $payment->delete();

        return redirect()->route('payment_receives.index')->with('success', 'Payment deleted successfully.');
    }
}
