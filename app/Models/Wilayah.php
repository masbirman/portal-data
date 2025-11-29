<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    public $timestamps = false;

    protected $table = 'wilayah';

    protected $fillable = [
        'nama',
        'logo',
        'urutan',
    ];

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class);
    }

    public function pelaksanaanAsesmen()
    {
        return $this->hasMany(PelaksanaanAsesmen::class);
    }
}
