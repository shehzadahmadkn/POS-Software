<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Account;

use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::orderBy('id', 'desc')->get();
        // Only get business accounts
        $accounts = Account::business()->get();
        $customers = Account::customers()->get();
        $vendors = Account::vendors()->get();
        
        return view('transfers.index', compact('transfers', 'accounts', 'customers', 'vendors'));
    }

    private function adjustBalance($type, $id, $amount, $isFrom)
    {
        if ($type === 'business') {
            $entity = Account::find($id);
            if ($isFrom) $entity->balance -= $amount;
            else $entity->balance += $amount;
            $entity->save();
        } elseif ($type === 'customer') {
            $entity = Account::customers()->find($id);
            if ($isFrom) $entity->balance -= $amount;
            else $entity->balance += $amount;
            $entity->save();
        } elseif ($type === 'vendor') {
            $entity = Account::vendors()->find($id);
            // Vendor is Liability: Credit (From) increases liability, Debit (To) decreases liability
            if ($isFrom) $entity->balance += $amount;
            else $entity->balance -= $amount;
            $entity->save();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'from' => 'required|string', // Format: type_id
            'to' => 'required|string|different:from', // Format: type_id
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        list($from_type, $from_id) = explode('_', $request->from);
        list($to_type, $to_id) = explode('_', $request->to);

        $this->adjustBalance($from_type, $from_id, $request->amount, true);
        $this->adjustBalance($to_type, $to_id, $request->amount, false);

        // Generate Ref: TR-001
        $latest = Transfer::orderBy('id', 'desc')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $refNo = 'TR-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        Transfer::create([
            'reference_no' => $refNo,
            'from_account_id' => $from_id,
            'to_account_id' => $to_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'note' => $request->note
        ]);

        return redirect()->route('transfers.index')->with('success', 'Transfer completed successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string|different:from',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        $transfer = Transfer::findOrFail($id);

        // 1. Revert old balances
        $this->adjustBalance($transfer->fromEntity->type, $transfer->from_account_id, $transfer->amount, false); // false = pretend it's To (reverses From)
        $this->adjustBalance($transfer->toEntity->type, $transfer->to_account_id, $transfer->amount, true); // true = pretend it's From (reverses To)

        // 2. Apply new balances
        list($from_type, $from_id) = explode('_', $request->from);
        list($to_type, $to_id) = explode('_', $request->to);

        $this->adjustBalance($from_type, $from_id, $request->amount, true);
        $this->adjustBalance($to_type, $to_id, $request->amount, false);

        // 3. Update record
        $transfer->update([
            'from_account_id' => $from_id,
            'to_account_id' => $to_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'note' => $request->note
        ]);

        return redirect()->route('transfers.index')->with('success', 'Transfer updated successfully.');
    }

    public function destroy($id)
    {
        $transfer = Transfer::findOrFail($id);

        // Revert old balances
        $this->adjustBalance($transfer->fromEntity->type, $transfer->from_account_id, $transfer->amount, false); // Reverse From
        $this->adjustBalance($transfer->toEntity->type, $transfer->to_account_id, $transfer->amount, true); // Reverse To

        $transfer->delete();

        return redirect()->route('transfers.index')->with('success', 'Transfer deleted successfully.');
    }
}
