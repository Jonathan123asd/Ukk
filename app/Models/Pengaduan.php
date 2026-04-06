<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use HasFactory;
class Pengaduan extends Model
{

    protected $table = 'pengaduans';

    protected $fillable = [
        'user_id',
        'judul',
        'urgensi',
        'kategori_id',
        'deskripsi',
        'lokasi',
        'status',
        'tanggal',
        'image',
    ];

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function respon()
    {
        return $this->hasMany(Respon::class, 'pengaduan_id');
    }

    public function kategori()
    {
        return $this->belongsTo(kategori::class, 'kategori_id');
    }
}
