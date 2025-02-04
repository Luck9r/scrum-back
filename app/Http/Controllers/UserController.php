<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
            ];
        });
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
        ];
    }

    public function destroy(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);
        $requestUser = $request->user();
        if ($user->id === $requestUser->id) {
            return response()->json(['message' => 'You cannot delete yourself.'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function update(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);
        $user->update($request->except('roles'));

        if ($request->has('roles')) {
            $roles = $request->input('roles');
            $user->roles()->sync(
                Role::query()->whereIn('name', $roles)->pluck('id')->toArray()
            );
        }

        return response()->json(['message' => 'User updated successfully.']);
    }
}
