<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
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
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Transaksi penjualan yang dilakukan oleh kasir ini.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Riwayat perubahan stok yang dilakukan oleh user ini.
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'user_id');
    }

    /**
     * Penyesuaian stok yang dilakukan oleh user ini.
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'user_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: filter hanya user aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter berdasarkan role.
     */
    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    // =========================================================================
    // ACCESSORS / HELPERS
    // =========================================================================

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah kasir.
     */
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    /**
     * Label role dalam Bahasa Indonesia.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'  => 'Administrator',
            'kasir'  => 'Kasir',
            default  => ucfirst($this->role),
        };
    }
}
