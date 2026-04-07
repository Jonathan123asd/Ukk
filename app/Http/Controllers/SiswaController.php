<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    // Dashboard siswa
    public function dashboard()
    {
        $user = Auth::user();


        $pengaduan = Pengaduan::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();


        $statistik = [
            'total' => Pengaduan::where('user_id', $user->id)->count(),
            'pending' => Pengaduan::where('user_id', $user->id)->where('status', 'pending')->count(),
            'proses' => Pengaduan::where('user_id', $user->id)->where('status', 'proses')->count(),
            'selesai' => Pengaduan::where('user_id', $user->id)->where('status', 'selesai')->count(),
        ];

        return view('siswa.dashboard', compact('pengaduan', 'statistik'));
    }

    // Form pengaduan
    public function form()
    {
        $kategori = Kategori::all();
        return view('siswa.form', compact('kategori'));
    }


    public function store(Request $request)
    {



        $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'required|string|min:10',
            'lokasi' => 'nullable|string|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urgensi' => 'required|in:rendah,sedang,tinggi'
        ]);

        $imagePath = null;


        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('pengaduan', 'public');
        }



        Pengaduan::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'urgensi' => $request->urgensi,
            'kategori_id' => $request->kategori_id,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'image' => $imagePath,
            'tanggal' => now()->toDateString(),
            'status' => 'pending'
        ]);



        return redirect()->route('siswa.history')
            ->with('success', 'Pengaduan berhasil dikirim!');
    }

    // History pengaduan
    public function history(Request $request)
    {
        $query = Pengaduan::with('respon.admin')
            ->where('user_id', Auth::id());

        // SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->search . '%')
                    ->orWhere('kategori_id', 'like', '%' . $request->search . '%')
                    ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }


        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pengaduan = $query->orderBy('created_at', 'desc')->get();

        return view('siswa.history', compact('pengaduan'));
    }

    public function show($id)
    {
        $pengaduan = Pengaduan::with(['respon.admin'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('siswa.detail', compact('pengaduan'));
    }

}



