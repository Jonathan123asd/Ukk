<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respon extends Model
{
    protected $table = 'respons';

    protected $fillable = [
        'pengaduan_id',
        'pesan',
        'admin_id',
    ];

     public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

     public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}
