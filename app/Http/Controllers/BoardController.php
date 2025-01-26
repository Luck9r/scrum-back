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
        $newStatusName = $request->input('status_name');

        // Create the new status
        $newStatus = Status::query()->create(['name' => $newStatusName]);

        // Get the current order of the existing status
        $currentOrder = $board->statuses()->where('status_id', $statusId)->first()->pivot->order;

        // Detach the old status and attach the new status with the same order
        $board->statuses()->detach($statusId);
        $board->statuses()->attach($newStatus->id, ['order' => $currentOrder]);

        // Reassign tasks from the old status to the new status
        $tasks = $board->tasks()->where('status_id', $statusId)->get();
        foreach ($tasks as $task) {
            $task->status_id = $newStatus->id;
            $task->save();
        }

        return response()->json(['message' => 'Status updated and tasks reassigned successfully.']);
    }

    public function addStatusToBoard(Request $request, $boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $statusName = $request->input('status_name');

        // Create the status if it doesn't already exist
        $status = Status::query()->firstOrCreate(['name' => $statusName]);

        // Get the current maximum order value for the board
        $maxOrder = $board->statuses()->max('order');

        // Attach the status to the board with the next order value
        $board->statuses()->attach($status->id, ['order' => $maxOrder + 1]);

        return response()->json(['message' => 'Status added to board with default order.']);
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
