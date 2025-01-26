<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function show($id)
    {
        return Task::query()->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
//            'slug' => 'required|unique:tasks',
            'title' => 'required',
//            'content' => 'required',
//            'status' => 'required',
//            'due_date' => 'required|date',
//            'priority' => 'required',
            'board_id' => 'required|integer',
        ]);
        $validated['creator_id'] = $request->user()->id;
        // get the first status of the board
        $validated['status_id'] = Board::query()->findOrFail($validated['board_id'])->statuses()->first()->id;

        return Task::query()->create($validated);
    }

    public function update(Request $request, $id)
    {
        $task = Task::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required',
            'content' => 'sometimes|required',
            'due_date' => 'sometimes|required|date',
            'assignee_id' => 'sometimes|required|integer',
            'priority_id' => 'sometimes|required|integer',
            'board_id' => 'sometimes|required|integer',
            'status_id' => 'sometimes|required|integer',
        ]);
        $task->update($validated);

        return $task;
    }

    public function destroy($id)
    {
        $task = Task::query()->findOrFail($id);
        $task->delete();

        return response()->noContent();
    }

    public function getTasksByUser(Request $request)
    {
        $userId = $request->user()->id;

        $tasks = Task::query()
            ->join('boards', 'tasks.board_id', '=', 'boards.id')
            ->join('board_user', 'boards.id', '=', 'board_user.board_id')
            ->where('board_user.user_id', $userId)
            ->select('tasks.*')
            ->get();

        return response()->json($tasks);
    }

    public function getTaskBySlug($slug)
    {
        $task = Task::query()->where('slug', $slug)->with(['priority', 'status', 'user'])->firstOrFail();

        $task = [
            'id' => $task->id,
            'slug' => $task->slug,
            'title' => $task->title,
            'content' => $task->content,
            'due_date' => $task->due_date,
            'priority' => $task->priority ? $task->priority->name : null,
            'priority_id' => $task->priority_id,
            'status' => $task->status ? $task->status->name : null,
            'status_id' => $task->status_id,
            'creator_name' => $task->creator ? $task->creator->name : null,
            'assignee_name' => $task->assignee ? $task->assignee->name : null,
            'creator_id' => $task->creator_id,
            'assignee_id' => $task->assignee_id,
            'board_id' => $task->board_id,
        ];

        return response()->json($task);
    }

    public function getTasksByBoard($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $tasks = $board->tasks()->with(['priority', 'status', 'user'])->get();

        $tasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'slug' => $task->slug,
                'title' => $task->title,
                'content' => $task->content,
                'due_date' => $task->due_date,
                'priority' => $task->priority ? $task->priority->name : null,
                'status' => $task->status ? $task->status->name : null,
                'creator_name' => $task->creator ? $task->creator->name : null,
                'assignee_name' => $task->assignee ? $task->assignee->name : null,
                'creator_id' => $task->creator_id,
                'assignee_id' => $task->assignee_id,
                'board_id' => $task->board_id,
            ];
        });

        return response()->json($tasks);
    }

    public function assignUser(Request $request, $taskId)
    {
        $task = Task::query()->findOrFail($taskId);
        $userId = $request->validate(['user_id' => 'required|exists:users,id'])['user_id'];

        $task->assignee_id = $userId;
        $task->save();

        return response()->json(['message' => 'User assigned to task successfully.']);
    }

    public function unassignUser($taskId)
    {
        $task = Task::query()->findOrFail($taskId);
        $task->assignee_id = null;
        $task->save();

        return response()->json(['message' => 'User unassigned from task successfully.']);
    }
}
