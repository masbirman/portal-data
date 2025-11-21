<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaksanaanAsesmen extends Model
{
    public $timestamps = false;

    protected $table = 'pelaksanaan_asesmen';

    protected $fillable = [
        'siklus_asesmen_id',
        'sekolah_id',
        'jumlah_peserta',
        'wilayah_id',
        'status_pelaksanaan',
        'moda_pelaksanaan',
        'partisipasi_literasi',
        'partisipasi_numerasi',
        'tempat_pelaksanaan',
        'nama_penanggung_jawab',
        'nama_proktor',
        'keterangan',
    ];

    public function siklusAsesmen()
    {
        return $this->belongsTo(SiklusAsesmen::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
