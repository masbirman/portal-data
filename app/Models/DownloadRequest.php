<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DownloadRequest extends Model
{
    protected $fillable = [
        'nama',
        'email',
        'instansi',
        'tujuan_penggunaan',
        'data_type',
        'tahun',
        'wilayah_id',
        'jenjang_pendidikan_id',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'download_token',
        'token_expires_at',
        'downloaded_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function jenjangPendidikan(): BelongsTo
    {
        return $this->belongsTo(JenjangPendidikan::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function generateDownloadToken(): void
    {
        $this->download_token = Str::random(64);
        $this->token_expires_at = now()->addDays(7); // Token valid 7 hari
        $this->save();
    }

    public function isTokenValid(): bool
    {
        return $this->download_token 
            && $this->token_expires_at 
            && $this->token_expires_at->isFuture()
            && $this->status === 'approved';
    }

    public function markAsDownloaded(): void
    {
        $this->downloaded_at = now();
        $this->save();
    }
}
