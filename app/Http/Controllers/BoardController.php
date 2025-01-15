<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function getTasks($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $tasks = $board->tasks;

        return response()->json($tasks);
    }
    public function getBoard($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        return response()->json($board);
    }

    public function index()
    {
        return Board::all();
    }

    public function show($id)
    {
        return Board::query()->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
        ]);

        return Board::query()->create($validated);
    }

    public function update(Request $request, $id)
    {
        $board = Board::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required',
        ]);

        $board->update($validated);

        return $board;
    }

    public function destroy($id)
    {
        $board = Board::query()->findOrFail($id);
        $board->delete();

        return response()->noContent();
    }

    public function getBoardsByUser(Request $request)
    {
        $user = $request->user();
        $boards = $user->boards;

        return response()->json($boards);
    }

    public function getStatusesByBoard($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $statuses = $board->tasks()->with('status')->get()->pluck('status')->unique('id');

        return response()->json($statuses);
    }
}
