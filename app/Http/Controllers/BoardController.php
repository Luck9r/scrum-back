<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Priority;
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
            'title' => 'required|string|max:255',
        ]);

        $board = Board::create($validated);

        return response()->json($board, 201);
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
        $statuses = $board->statuses;

        return response()->json($statuses);
    }

    public function getUsersByBoard($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $users = $board->users;

        return response()->json($users);
    }

    public function getPrioritiesByBoard($boardId)
    {
//        $board = Board::query()->findOrFail($boardId);
//        $priorities = $board->priorities;
        // for now priorities are common for all boards
        $priorities = Priority::all();
        return response()->json($priorities);
    }
}
