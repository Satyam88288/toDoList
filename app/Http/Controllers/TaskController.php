<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function getAllTask()
    {
        $tasks = Task::all();
        return response()->json(['success' => true,'data' => $tasks]);
    }

    public function getTask()
    {
        $tasks = Task::where('is_completed','0')->get();
        return response()->json(['success' => true,'data' => $tasks]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|unique:tasks,title|max:255',
        ]);
        $tasks = Task::create(['title' => $request->title]);
        return response()->json(['success' => true,'message' => 'Task added successfully!','data' => $tasks]);
    }

    public function update(Request $request)
    {
        $task = Task::find($request->taskId);
        $task->is_completed = !$task->is_completed;
        $task->save();
        return response()->json(['success' => true,'message' => 'Task updated successfully!']);
    }
    
    public function destroy(Request $request)
    {
        Task::where('id',$request->taskId)->delete();
        return response()->json(['success' => true,'message' => 'Task deleted successfully!']);
    }
}
