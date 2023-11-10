<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;


class TaskController extends Controller
{
    public function sort(Request $request)
    {
        $cat = $request->query('cat'); // Pobierz parametr 'cat' z zapytania GET
        $count = Task::count();  // Zlicza wszystkie rekordy w tabeli "tasks"
        $query = Task::query();
        $perPage = $request->input('per_page', 5);

        if ($cat) {
            $query->where('category_id', $cat);
        }
        
// $tasks = Task::active()->get();
    $sortDirection = strtolower($request->input('sort_direction', 'asc')); // Domyślnie DESC, ale można ustawić inny kierunek w zapytaniu
    $tasks = $query->orderBy('created_at', $sortDirection)->paginate($perPage);

    return response()->json(['tasks' => $tasks, 'cout'=>$count], 200);
    }

    public function index(Request $request)
    {
        $cat = $request->query('cat');
        $query = Task::query();
        $tasks = $query->orderBy('created_at', 'DESC')->get();

        // $tasks = Task::all();
        return response()->json(['tasks' => $tasks, 'dwa'=>'test', 'cat'=>$cat], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'completed' => 'boolean',
        ]);

        $task = Task::create($request->all());
        return response()->json(['task' => $task], 201);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json(['task' => $task], 200);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'completed' => 'boolean',
        ]);

        $task->update($request->all());
        return response()->json(['task' => $task], 200);
    }

    public function destroy($id)
    {
        Task::destroy($id);
        return response()->json(null, 204);
    }
}
