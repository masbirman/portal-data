<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'wilayah';

    protected $fillable = [
        'nama',
        'logo',
        'urutan',
        'latitude',
        'longitude',
        'geometry',
    ];

    protected $casts = [
        'geometry' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('urutan', 'asc')->orderBy('nama', 'asc');
        });
    }

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class);
    }

    public function pelaksanaanAsesmen()
    {
        return $this->hasMany(PelaksanaanAsesmen::class);
    }
}
