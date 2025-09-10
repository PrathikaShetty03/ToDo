<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
// Show Blade page with todos
    public function index()
    {
        return view('welcome', ['todos' => Todo::orderBy('id', 'ASC')->get()]);
    }

    // Return all todos (API)
    public function getTodos()
    {
        return response()->json(Todo::orderBy('id', 'DESC')->get());
    }

    // Store new todo
    public function store(Request $request)
    {
        $todo = Todo::create([
            'name' => $request->name,
        ]);
        return response()->json($todo);
    }

    // Show single todo (for Edit)
    public function show($id)
    {
        $todo = Todo::findOrFail($id);
        return response()->json($todo);
    }

    // Update todo
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update([
            'name' => $request->name,
        ]);
        return response()->json($todo);
    }

    // Delete todo
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return response()->json(['success' => true]);
    }
}
