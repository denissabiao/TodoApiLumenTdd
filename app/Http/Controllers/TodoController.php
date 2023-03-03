<?php

namespace App\Http\Controllers;

use App\Models\Todo;
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
        $create_todo = Todo::find($id);
        if (!$create_todo) {
            return response()->json(['error' => 'data not found'], 404);
        }
        return response()->json($create_todo, 200);
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

        $todo = Todo::find($id);
        $todo->fill($request->all());
        $todo->save();
        return response()->json($todo, 201);
    }

    public function destroy($id)
    {

        try {
            Todo::findOrFail($id)->delete();
            return response()->json([], 204);
        } catch (\Exception $ex) {
            return response()->json([
                'data' => 'not found',
                'message' => $ex->getMessage(),
            ], 404);
        }





    }




//
}