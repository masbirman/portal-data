<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global Scope untuk filter data berdasarkan sekolah
 * yang terkait dengan User Sekolah.
 *
 * Scope ini akan otomatis memfilter query berdasarkan:
 * - sekolah_id = user.sekolah_id (untuk model yang memiliki sekolah_id)
 * - id = user.sekolah_id (untuk model Sekolah itu sendiri)
 */
class SekolahScope implements Scope
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

        // Skip jika Admin Wilayah (menggunakan WilayahJenjangScope)
        if ($user->isAdminWilayah()) {
            return;
        }

        // Apply filter untuk User Sekolah
        if ($user->isUserSekolah()) {
            $sekolahId = $user->sekolah_id;

            if (!$sekolahId) {
                // Jika tidak ada sekolah yang terkait, tidak tampilkan data apapun
                $builder->whereRaw('1 = 0');
                return;
            }

            // Jika model adalah Sekolah, filter berdasarkan id
            if ($model->getTable() === 'sekolah') {
                $builder->where($model->getTable() . '.id', $sekolahId);
            }
            // Jika model memiliki sekolah_id, filter berdasarkan sekolah_id
            elseif ($this->hasSekolahColumn($model)) {
                $builder->where($model->getTable() . '.sekolah_id', $sekolahId);
            }
        }
    }

    /**
     * Check if model has sekolah_id column
     */
    protected function hasSekolahColumn(Model $model): bool
    {
        return in_array('sekolah_id', $model->getFillable())
            || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'sekolah_id');
    }
}
