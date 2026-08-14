<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expense_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:expense_categories'
        ]);

        ExpenseCategory::create($request->only('name'));

        return redirect()->route('expense_categories.index')->with('success', 'Expense Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:expense_categories,name,' . $category->id
        ]);

        $category->update($request->only('name'));

        return redirect()->route('expense_categories.index')->with('success', 'Expense Category updated successfully.');
    }
}
