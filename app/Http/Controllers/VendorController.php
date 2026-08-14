<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Account::vendors()->orderBy('id', 'desc')->get();
        return view('vendors.index', compact('vendors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $vendor = Account::vendors()->findOrFail($id);
        $vendor->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully!');
    }
}
