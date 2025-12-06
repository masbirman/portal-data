<?php

namespace App\Models;

use App\Models\Scopes\SekolahScope;
use App\Models\Scopes\WilayahJenjangScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;
    /**
     * Boot the model and apply global scopes
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new WilayahJenjangScope());
        static::addGlobalScope(new SekolahScope());
    }

    public $timestamps = false;

    protected $table = 'sekolah';

    protected $fillable = [
        'kode_sekolah',
        'npsn',
        'nama',
        'tahun',
        'jenjang_pendidikan_id',
        'wilayah_id',
        'status_sekolah',
        'latitude',
        'longitude',
        'alamat',
    ];

    protected $casts = [
        'tahun' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
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
