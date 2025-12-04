<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    public static function log(string $action, string $description, $model = null, array $properties = []): ?self
    {
        try {
            return self::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model?->id,
                'description' => $description,
                'properties' => $properties ?: null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist or database is not connected
            \Log::debug('ActivityLog failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getActionBadgeColorAttribute(): string
    {
        return match ($this->action) {
            'approve' => 'success',
            'reject' => 'danger',
            'backup' => 'info',
            'restore' => 'warning',
            'login' => 'primary',
            'logout' => 'gray',
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            default => 'gray',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'approve' => 'heroicon-o-check-circle',
            'reject' => 'heroicon-o-x-circle',
            'backup' => 'heroicon-o-cloud-arrow-up',
            'restore' => 'heroicon-o-cloud-arrow-down',
            'login' => 'heroicon-o-arrow-right-on-rectangle',
            'logout' => 'heroicon-o-arrow-left-on-rectangle',
            'create' => 'heroicon-o-plus-circle',
            'update' => 'heroicon-o-pencil-square',
            'delete' => 'heroicon-o-trash',
            default => 'heroicon-o-document-text',
        };
    }
}
