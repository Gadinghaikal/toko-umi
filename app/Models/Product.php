<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'category_id',
        'name',
        'unit',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'stock'          => 'integer',
        'min_stock'      => 'integer',
        'is_active'      => 'boolean',
    ];

    // =========================================================================
    // BOOT
    // =========================================================================

    /**
     * Boot method: auto-generate kode produk unik sebelum menyimpan.
     * Format: PRD-XXXX (4 digit, contoh: PRD-0001)
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->code)) {
                $product->code = static::generateProductCode();
            }
        });
    }

    /**
     * Generate kode produk otomatis berdasarkan ID terakhir.
     * Format: PRD-XXXX
     */
    public static function generateProductCode(): string
    {
        $lastProduct = static::orderByDesc('id')->first();
        $nextNumber  = $lastProduct ? ($lastProduct->id + 1) : 1;

        return 'PRD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Kategori produk ini.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Seluruh riwayat perubahan stok produk ini.
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_id')->orderByDesc('created_at');
    }

    /**
     * Seluruh penyesuaian stok produk ini.
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'product_id')->orderByDesc('created_at');
    }

    /**
     * Item-item transaksi yang mengandung produk ini.
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'product_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: filter hanya produk aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter produk yang stoknya di bawah atau sama dengan min_stock.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /**
     * Scope: filter produk yang stoknya habis (0).
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Scope: cari berdasarkan nama atau kode produk.
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('code', 'like', "%{$keyword}%");
        });
    }

    // =========================================================================
    // ACCESSORS / HELPERS
    // =========================================================================

    /**
     * Cek apakah stok produk dalam kondisi menipis (di bawah min_stock).
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Cek apakah produk sudah habis stoknya.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Hitung margin keuntungan per unit (nominal).
     */
    public function getProfitMarginAttribute(): float
    {
        return (float) $this->selling_price - (float) $this->purchase_price;
    }

    /**
     * Hitung persentase margin keuntungan.
     */
    public function getProfitMarginPercentAttribute(): float
    {
        if ((float) $this->purchase_price <= 0) {
            return 0.0;
        }

        return round(
            (((float) $this->selling_price - (float) $this->purchase_price) / (float) $this->purchase_price) * 100,
            2
        );
    }

    /**
     * Nilai total stok berdasarkan harga beli.
     */
    public function getStockValueAttribute(): float
    {
        return (float) $this->purchase_price * $this->stock;
    }

    /**
     * Status stok dalam bentuk teks.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }

        if ($this->stock <= $this->min_stock) {
            return 'menipis';
        }

        return 'aman';
    }

    /**
     * Badge color Bootstrap berdasarkan status stok.
     */
    public function getStockStatusBadgeAttribute(): string
    {
        return match ($this->stock_status) {
            'habis'   => 'danger',
            'menipis' => 'warning',
            'aman'    => 'success',
            default   => 'secondary',
        };
    }
}
