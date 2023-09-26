<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = auth()->user();

        // hanya admin yang dapat mengakses
        if ($user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        $users = User::all();

        return response()->json([
            'status' => 'success',
            'data'   => $users,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // hanya admin yang dapat mengakses
        if ($user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|string|min:8',
            'role'     => 'required|in:user,editor,admin',
        ]);

        $newUser = new User([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
        ]);

        $newUser->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully',
            'data'    => $newUser,
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();

        // hanya admin yang dapat mengakses
        if ($user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        $requestedUser = User::find($id);

        if (!$requestedUser) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $requestedUser,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        // hanya admin yang dapat mengakses
        if ($user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role'     => 'required|in:user,editor,admin',
        ]);

        $editedUser = User::find($id);

        if (!$editedUser) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $editedUser->name = $request->name;
        $editedUser->email = $request->email;

        if ($request->has('password')) {
            $editedUser->password = bcrypt($request->password);
        }

        $editedUser->role = $request->role;
        $editedUser->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'User updated successfully',
            'data'    => $editedUser,
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        // Pastikan hanya admin yang dapat mengakses tindakan ini
        if ($user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }

        $deletedUser = User::find($id);

        if (!$deletedUser) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $deletedUser->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User deleted successfully',
            'data'    => $deletedUser,
        ]);
    }

}