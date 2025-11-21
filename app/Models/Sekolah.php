<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    public $timestamps = false;

    protected $table = 'sekolah';

    protected $fillable = [
        'kode_sekolah',
        'nama',
        'tahun',
        'jenjang_pendidikan_id',
        'wilayah_id',
        'status_sekolah',
    ];

    protected $casts = [
        'tahun' => 'array',
    ];

    public function jenjangPendidikan()
    {
        return $this->belongsTo(JenjangPendidikan::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function pelaksanaanAsesmen()
    {
        return $this->hasMany(PelaksanaanAsesmen::class);
    }
}
