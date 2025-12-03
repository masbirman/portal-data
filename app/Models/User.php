<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'role',
        'sekolah_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==================== ROLE CHECKER METHODS ====================

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminWilayah(): bool
    {
        return $this->role === 'admin_wilayah';
    }

    public function isUserSekolah(): bool
    {
        return $this->role === 'user_sekolah';
    }

    // ==================== PANEL ACCESS ====================

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->isSuperAdmin(),
            'wilayah' => $this->isAdminWilayah(),
            'sekolah' => $this->isUserSekolah(),
            default => false,
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? \Storage::disk('public')->url($this->avatar) : null;
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Wilayah yang ditugaskan untuk Admin Wilayah (many-to-many)
     */
    public function wilayahs(): BelongsToMany
    {
        return $this->belongsToMany(Wilayah::class, 'user_wilayah')
            ->withTimestamps();
    }

    /**
     * Jenjang Pendidikan yang ditugaskan untuk Admin Wilayah (many-to-many)
     */
    public function jenjangs(): BelongsToMany
    {
        return $this->belongsToMany(JenjangPendidikan::class, 'user_jenjang')
            ->withTimestamps();
    }

    /**
     * Sekolah yang terkait untuk User Sekolah (belongs-to)
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * Permissions untuk User Sekolah (many-to-many)
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withTimestamps();
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if user has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Super Admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin Wilayah has all permissions within their scope
        if ($this->isAdminWilayah()) {
            return true;
        }

        // User Sekolah needs explicit permission
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Get array of wilayah IDs assigned to this user
     */
    public function getWilayahIds(): array
    {
        return $this->wilayahs()->pluck('wilayah.id')->toArray();
    }

    /**
     * Get array of jenjang pendidikan IDs assigned to this user
     */
    public function getJenjangIds(): array
    {
        return $this->jenjangs()->pluck('jenjang_pendidikan.id')->toArray();
    }
}
