<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\kategori;
use App\Models\Pengaduan;
use App\Models\User;
use App\Models\Respon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard(Request $request)
    {

        $filters = $request->only(['search', 'status', 'kategori', 'tanggal']);

        $query = Pengaduan::with('user', 'kategori');


        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%");
                    });
            });
        }


        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }


        if (!empty($filters['kategori'])) {
            $query->where('kategori_id', $filters['kategori']);
        }


        if (!empty($filters['tanggal'])) {
            $query->whereDate('tanggal', $filters['tanggal']);
        }

        $pengaduan  = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();


        $statistik = [
            'total' => Pengaduan::count(),
            'pending' => Pengaduan::where('status', 'pending')->count(),
            'proses' => Pengaduan::where('status', 'proses')->count(),
            'selesai' => Pengaduan::where('status', 'selesai')->count(),
        ];

        $kategoriList = kategori::all();

        return view('admin.dashboard', compact('pengaduan', 'statistik', 'kategoriList'));
    }

    public function detail($id)
    {
        $pengaduan = Pengaduan::with(['user', 'respon.admin','kategori'])->findOrFail($id);

        return view('admin.detail', compact('pengaduan', ));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,proses,selesai'
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->update(['status' => $request->status]);

        return back()->with('success', 'Status berhasil diperbarui!');
    }


    public function storeRespon(Request $request)
    {
        $request->validate([
            'pengaduan_id' => 'required|exists:pengaduans,id',
            'pesan' => 'required|string|min:5'
        ]);

        Respon::create([
            'pengaduan_id' => $request->pengaduan_id,
            'pesan' => $request->pesan,
            'admin_id' => Auth::id()
        ]);

        return redirect()
            ->route('admin.dashboard', $request->pengaduan_id)
            ->with('success', 'Respon berhasil ditambahkan!');
    }
}





