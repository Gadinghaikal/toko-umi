<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class StockHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'purchase_price',
        'reference',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after'  => 'integer',
        'purchase_price'  => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Produk yang terkait dengan riwayat stok ini.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * User (kasir/admin) yang melakukan perubahan stok ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: filter hanya riwayat stok masuk (pembelian/penambahan).
     */
    public function scopeStockIn(Builder $query): Builder
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope: filter hanya riwayat stok keluar (penjualan).
     */
    public function scopeStockOut(Builder $query): Builder
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope: filter hanya riwayat penyesuaian stok.
     */
    public function scopeAdjustment(Builder $query): Builder
    {
        return $query->where('type', 'adjustment');
    }

    /**
     * Scope: filter berdasarkan rentang tanggal.
     */
    public function scopeDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    // =========================================================================
    // ACCESSORS / HELPERS
    // =========================================================================

    /**
     * Label tipe perubahan dalam Bahasa Indonesia.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in'         => 'Stok Masuk',
            'out'        => 'Stok Keluar',
            'adjustment' => 'Penyesuaian',
            default      => ucfirst($this->type),
        };
    }

    /**
     * Warna badge Bootstrap berdasarkan tipe.
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'in'         => 'success',
            'out'        => 'danger',
            'adjustment' => 'warning',
            default      => 'secondary',
        };
    }

    /**
     * Tampilkan quantity_change dengan tanda + atau -.
     */
    public function getFormattedQuantityChangeAttribute(): string
    {
        if ($this->quantity_change > 0) {
            return '+' . $this->quantity_change;
        }

        return (string) $this->quantity_change;
    }
}
