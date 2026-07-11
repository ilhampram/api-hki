<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Document extends Model
{
    use SoftDeletes;

        protected $fillable = [
        'user_id',
        'nomor_hki',
        'judul',
        'pemilik',
        'category_id',
        'tanggal_daftar',
        'status',
        'file_dokumen'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}