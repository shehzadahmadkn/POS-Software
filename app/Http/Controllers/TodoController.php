<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', auth()->id())
            ->orderBy('is_completed', 'asc')
            ->orderBy('due_date', 'asc')
            ->get();

        return view('todos.index', compact('todos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:500',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);

        Todo::create([
            'user_id' => auth()->id(),
            'task' => $request->task,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'is_completed' => false
        ]);

        return redirect()->route('todos.index')->with('success', 'Task added successfully.');
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'task' => 'required|string|max:500',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);

        $todo->update([
            'task' => $request->task,
            'priority' => $request->priority,
            'due_date' => $request->due_date
        ]);

        return redirect()->route('todos.index')->with('success', 'Task updated successfully.');
    }

    public function toggle($id)
    {
        $todo = Todo::where('user_id', auth()->id())->findOrFail($id);
        $todo->update(['is_completed' => !$todo->is_completed]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $todo = Todo::where('user_id', auth()->id())->findOrFail($id);
        $todo->delete();

        return redirect()->route('todos.index')->with('success', 'Task deleted successfully.');
    }
}
