<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenjangPendidikan extends Model
{
    public $timestamps = false;

    protected $table = 'jenjang_pendidikan';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class);
    }
}
