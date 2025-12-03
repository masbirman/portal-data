<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    // ==================== PERMISSION CONSTANTS ====================

    const VIEW_SEKOLAH_DATA = 'view_sekolah_data';
    const VIEW_ASESMEN_DATA = 'view_asesmen_data';
    const DOWNLOAD_REPORT = 'download_report';
    const VIEW_STATISTICS = 'view_statistics';

    /**
     * Get all available permissions
     */
    public static function getAvailablePermissions(): array
    {
        return [
            self::VIEW_SEKOLAH_DATA => 'Lihat Data Sekolah',
            self::VIEW_ASESMEN_DATA => 'Lihat Data Asesmen',
            self::DOWNLOAD_REPORT => 'Download Laporan',
            self::VIEW_STATISTICS => 'Lihat Statistik',
        ];
    }

    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    /**
     * Users yang memiliki permission ini
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permission')
            ->withTimestamps();
    }
}
