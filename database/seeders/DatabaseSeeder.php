<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengaduan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $Admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
            'kelas' => null,
        ]);

        $Admin = User::create([
            'name' => 'Guru Sekolah',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
            'kelas' => null,
        ]);
    

        $Siswa = User::create([
            'name' => 'Jonathan',
            'email' => 'siswa@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'status' => 'approved',
            'kelas' => 'XII RPL 1',
        ]);
    }
}

