<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function home ()
    {
        return view('layouts.home');
    }
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();


            if ($user->role ===  'admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }


            if ($user->role === 'siswa') {
                if ($user->status === 'pending') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Akun Anda masih menunggu persetujuan admin.',
                    ]);
                }

                if ($user->status === 'rejected') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Akun Anda ditolak oleh admin.',
                    ]);
                }


                $request->session()->regenerate();
                return redirect()->route('siswa.dashboard');
            }
        }


        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}


