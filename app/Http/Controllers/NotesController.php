<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notes;
use App\Http\Middleware\CheckRole;

class NotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware(CheckRole::class . ':user,editor,admin');
    }

    public function index()
    {
        $user = auth()->user();

        // Jika pengguna adalah admin, tampilkan semua catatan
        if ($user->role === 'admin') {
            $notes = Notes::all();
        } else {
            // Hanya tampilkan catatan milik pengguna yang sedang terautentikasi
            $notes = $user->notes;
        }

        return response()->json([
            'status'    => 'success',
            'data'      => $notes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $data = new Notes([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        $user->notes()->save($data);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data created successfully',
            'data'      => $data,
        ]);
    }

    public function show($id)
    {
        $data = Notes::find($id);

        $user = auth()->user();
        // Jika pengguna adalah admin atau pengguna memiliki catatan ini
        if ($user->role === 'admin' || $user->id === $data->user_id) {
            return response()->json([
                'status' => 'success',
                'data'   => $data,
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:255',
        ]);

        $data = Notes::find($id);

        $user = auth()->user();

       // Jika pengguna adalah admin atau pemilik catatan ini
        if ($user->role === 'admin' || $user->id === $data->user_id) {

            $data->title = $request->title;
            $data->description = $request->description;
            $data->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data updated successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }
    }

    public function destroy($id)
    {
        $data = Notes::find($id);

        // Pastikan hanya pengguna yang memiliki catatan ini yang dapat menghapusnya
        $user = auth()->user();

        // Jika pengguna adalah admin atau pemilik catatan ini
        if ($user->role === 'admin' || $user->id === $data->user_id) {
            $data->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data deleted successfully',
                'data'    => $data,
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Permission denied'], 403);
        }
    }
}