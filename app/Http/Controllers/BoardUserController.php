<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;

class BoardUserController extends Controller
{
    public function assignUser(Request $request, $boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $userId = $request->input('user_id');
        $board->users()->syncWithoutDetaching($userId);

        return response()->json(['message' => 'User assigned to board successfully.']);
    }

    public function unassignUser(Request $request, $boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $userId = $request->input('user_id');
        $board->users()->detach($userId);

        return response()->json(['message' => 'User unassigned from board successfully.']);
    }
}
