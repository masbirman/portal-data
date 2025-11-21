<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiklusAsesmen extends Model
{
    public $timestamps = false;

    protected $table = 'siklus_asesmen';

    protected $fillable = [
        'tahun',
        'nama',
    ];

    public function pelaksanaanAsesmen()
    {
        return $this->hasMany(PelaksanaanAsesmen::class);
    }
}
