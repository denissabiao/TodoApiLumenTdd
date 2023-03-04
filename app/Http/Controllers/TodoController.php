<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodoController extends Controller
{

    public function __construct()
    {
        //
    }

    public function index()
    {
        $create_todo = Todo::paginate(5);
        return response()->json($create_todo, 200);
    }

    public function show($id)
    {
        try {
            $create_todo = Todo::findOrFail($id);
            if (!$create_todo) {
                return response()->json(['error' => 'data not found'], 404);
            }
            return response()->json($create_todo, 200);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => 'not found',
                'message' => $ex->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:8',
            'description' => 'required',
        ]);

        $create_todo = Todo::create($request->all());
        return response()->json($create_todo, 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:8',
            'description' => 'required',
        ]);

        try {
            $todo = Todo::findOrFail($id);
            $todo->fill($request->all());
            $todo->save();
            return response()->json($todo, 201);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => 'not found',
                'message' => $ex->getMessage(),
            ], 404);
        }
    }

    public function destroy($id)
    {

        try {
            Todo::findOrFail($id)->delete();
        } catch (\Exception $ex) {
            return response()->json([
                'data' => 'not found',
                'message' => $ex->getMessage(),
            ], 404);
        }

        return response()->json([], 204);
    }

    public function postChangeStatusTodo($id, $status)
    {

        if (!$this->validateStatus($status)) {
            return response()->json(['error' => 'status not available: done, undone'], 422);
        }

        try {
            $todo = Todo::findOrFail($id);

            $status == 'done' ? $this->done($todo) : $this->undone($todo);

            return response()->json($todo);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => 'not found',
                'message' => $ex->getMessage(),
            ], 404);
        }

    }

    public function validateStatus($status)
    {
        return in_array($status, ['done', 'undone']);
    }

    public function done($todo)
    {
        $todo->done = '1';
        $todo->done_at = Carbon::now();
        $todo->save();
    }

    public function undone($todo)
    {
        $todo->done = '0';
        $todo->done_at = null;
        $todo->save();
    }


//
}