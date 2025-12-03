<?php

namespace App\Models;

use App\Models\Scopes\SekolahScope;
use App\Models\Scopes\WilayahJenjangScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PelaksanaanAsesmen extends Model
{
    /**
     * Boot the model and apply global scopes
     */
    protected static function booted(): void
    {
        // Custom scope untuk PelaksanaanAsesmen karena jenjang ada di relasi sekolah
        static::addGlobalScope('wilayah_jenjang', function (Builder $builder) {
            $user = auth()->user();

            if (!$user || $user->isSuperAdmin()) {
                return;
            }

            if ($user->isAdminWilayah()) {
                $wilayahIds = $user->getWilayahIds();
                $jenjangIds = $user->getJenjangIds();

                if (!empty($wilayahIds)) {
                    $builder->whereIn('pelaksanaan_asesmen.wilayah_id', $wilayahIds);
                } else {
                    $builder->whereRaw('1 = 0');
                }

                // Filter jenjang melalui relasi sekolah
                if (!empty($jenjangIds)) {
                    $builder->whereHas('sekolah', function ($q) use ($jenjangIds) {
                        $q->whereIn('jenjang_pendidikan_id', $jenjangIds);
                    });
                } else {
                    $builder->whereRaw('1 = 0');
                }
            }
        });

        static::addGlobalScope(new SekolahScope());
    }

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
