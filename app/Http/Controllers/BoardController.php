<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Priority;
use App\Models\Status;
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

    public function updateStatus(Request $request, $boardId, $statusId)
    {
        $board = Board::query()->findOrFail($boardId);
        $status = $board->statuses()->where('status_id', $statusId)->firstOrFail();

        $status->update(['name' => $request->input('name')]);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function addStatusToBoard(Request $request, $boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $statusName = $request->input('name');

        $status = Status::query()->create(['name' => $statusName]);


        // Get the current maximum order value for the board
        $maxOrder = $board->statuses()->max('order');

        // Attach the status to the board with the next order value
        $board->statuses()->attach($status->id, ['order' => $maxOrder + 1]);

        return response()->json($status);
    }

    public function destroyStatus($boardId, $statusId)
    {
        $board = Board::query()->findOrFail($boardId);
        $status = $board->statuses()->where('status_id', $statusId)->firstOrFail();

        // Find the first status of the board
        $firstStatus = $board->statuses()->orderBy('order')->first();

        // Reassign tasks associated with the status to the first status
        $status->tasks()->update(['status_id' => $firstStatus->id]);

        // Detach the status from the board
        $board->statuses()->detach($statusId);

        // Delete the status
        $status->delete();

        return response()->json(['message' => 'Status deleted and tasks reassigned successfully.']);
    }

    public function updateStatusOrder(Request $request, $boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $statuses = $request->input('statuses');

        foreach ($statuses as $order => $statusId) {
            $board->statuses()->updateExistingPivot($statusId, ['order' => $order]);
        }

        return response()->json(['message' => 'Status order updated successfully.']);
    }

    public function getStatusesByBoard($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $statuses = $board->statuses()->orderBy('order')->get();

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
