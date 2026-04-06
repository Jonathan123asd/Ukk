<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function pending()
    {
        $users = User::where('status', 'pending')
            ->where('role', 'siswa')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.pending', compact('users'));
    }


    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);

        return back()->with('success', 'Akun ' . $user->name . ' berhasil disetujui!');
    }


    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);

        return back()->with('success', 'Akun ' . $user->name . ' telah ditolak.');
    }


    public function index()
    {
         // Cek manual saja
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Hanya untuk admin');
        }

        $users = User::where('role', 'siswa')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingCount = User::where('status', 'pending')->where('role', 'siswa')->count();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

}





