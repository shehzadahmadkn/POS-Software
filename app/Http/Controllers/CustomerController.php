<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Account::customers()->orderBy('id', 'desc')->get();
        return view('customers.index', compact('customers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = Account::customers()->findOrFail($id);
        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }
}
