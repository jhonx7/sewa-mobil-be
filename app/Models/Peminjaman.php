<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    protected $fillable = [
        'tgl_mulai',
        'tgl_selesai',
        'mobil_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

}
