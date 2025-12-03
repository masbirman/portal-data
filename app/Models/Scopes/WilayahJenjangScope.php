<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global Scope untuk filter data berdasarkan wilayah dan jenjang pendidikan
 * yang ditugaskan kepada Admin Wilayah.
 *
 * Scope ini akan otomatis memfilter query berdasarkan:
 * - wilayah_id IN user.getWilayahIds()
 * - jenjang_pendidikan_id IN user.getJenjangIds()
 */
class WilayahJenjangScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Skip jika tidak ada user yang login
        if (!$user) {
            return;
        }

        // Skip jika Super Admin (akses penuh)
        if ($user->isSuperAdmin()) {
            return;
        }

        // Apply filter untuk Admin Wilayah
        if ($user->isAdminWilayah()) {
            $wilayahIds = $user->getWilayahIds();
            $jenjangIds = $user->getJenjangIds();

            // Filter berdasarkan wilayah
            if (!empty($wilayahIds)) {
                $builder->whereIn($model->getTable() . '.wilayah_id', $wilayahIds);
            } else {
                // Jika tidak ada wilayah yang ditugaskan, tidak tampilkan data apapun
                $builder->whereRaw('1 = 0');
            }

            // Filter berdasarkan jenjang pendidikan (jika model memiliki kolom ini)
            if ($this->hasJenjangColumn($model) && !empty($jenjangIds)) {
                $builder->whereIn($model->getTable() . '.jenjang_pendidikan_id', $jenjangIds);
            } elseif ($this->hasJenjangColumn($model) && empty($jenjangIds)) {
                // Jika tidak ada jenjang yang ditugaskan, tidak tampilkan data apapun
                $builder->whereRaw('1 = 0');
            }
        }
    }

    /**
     * Check if model has jenjang_pendidikan_id column
     */
    protected function hasJenjangColumn(Model $model): bool
    {
        return in_array('jenjang_pendidikan_id', $model->getFillable())
            || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'jenjang_pendidikan_id');
    }
}
