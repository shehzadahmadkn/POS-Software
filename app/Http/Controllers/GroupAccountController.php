<?php

namespace App\Http\Controllers;

use App\Models\GroupAccount;
use App\Models\Account;

use Illuminate\Http\Request;

class GroupAccountController extends Controller
{
    public function index()
    {
        $groupAccounts = GroupAccount::with(['customer', 'vendor'])->get();
        $customers = Account::customers()->get();
        $vendors = Account::vendors()->get();
        return view('group_accounts.index', compact('groupAccounts', 'customers', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        GroupAccount::create($request->all());

        return redirect()->route('group_accounts.index')->with('success', 'Group Account created successfully.');
    }

    public function destroy(GroupAccount $groupAccount)
    {
        $groupAccount->delete();
        return redirect()->route('group_accounts.index')->with('success', 'Group Account deleted successfully.');
    }
}
