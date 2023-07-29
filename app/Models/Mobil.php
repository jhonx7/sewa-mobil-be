<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    use HasFactory;
    protected $fillable = [
        'merek',
        'model',
        'nomor_plat',
        'tarif',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'peminjaman_id');
    }

}
