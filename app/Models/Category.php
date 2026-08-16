<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =========================================================================
    // BOOT
    // =========================================================================

    /**
     * Boot method: auto-generate slug dari name sebelum menyimpan.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Produk-produk yang termasuk dalam kategori ini.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Produk aktif dalam kategori ini.
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id')->where('is_active', true);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: filter hanya kategori aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // =========================================================================
    // ACCESSORS / HELPERS
    // =========================================================================

    /**
     * Jumlah total produk dalam kategori ini.
     */
    public function getProductCountAttribute(): int
    {
        return $this->products()->count();
    }

    /**
     * Jumlah produk aktif dalam kategori ini.
     */
    public function getActiveProductCountAttribute(): int
    {
        return $this->products()->where('is_active', true)->count();
    }
}
