<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'kelas' => 'required|string|max:20',
            'nis' => 'required|string|max:20|unique:users',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'kelas' => $request->kelas,
            'nis' => $request->nis,
            'status' => 'pending',
        ]);

        return redirect()->route('register.success')
            ->with('success', 'Registrasi berhasil! Akun Anda menunggu persetujuan admin.');
    }

    public function showSuccessPage()
    {
        if (! session('success')) {
            return redirect()->route('login');
        }

        return view('auth.register-succes');
    }
}
