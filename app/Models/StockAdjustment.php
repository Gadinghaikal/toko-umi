<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class StockAdjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'adjustment_code',
        'user_id',
        'product_id',
        'stock_before',
        'stock_after',
        'difference',
        'reason',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock_before' => 'integer',
        'stock_after'  => 'integer',
        'difference'   => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Daftar alasan penyesuaian yang valid.
     */
    public const REASONS = [
        'koreksi_fisik'      => 'Koreksi Fisik (Stock Opname)',
        'barang_rusak'       => 'Barang Rusak / Cacat',
        'barang_kadaluarsa'  => 'Barang Kadaluarsa',
        'kesalahan_input'    => 'Kesalahan Input Data',
        'lainnya'            => 'Lainnya',
    ];

    // =========================================================================
    // BOOT
    // =========================================================================

    /**
     * Boot method: auto-generate kode penyesuaian unik.
     * Format: ADJ-YYYYMMDD-XXX (contoh: ADJ-20240101-001)
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (StockAdjustment $adjustment) {
            if (empty($adjustment->adjustment_code)) {
                $adjustment->adjustment_code = static::generateAdjustmentCode();
            }

            // Hitung difference otomatis
            $adjustment->difference = $adjustment->stock_after - $adjustment->stock_before;
        });
    }

    /**
     * Generate kode penyesuaian stok otomatis.
     * Format: ADJ-YYYYMMDD-XXX
     */
    public static function generateAdjustmentCode(): string
    {
        $today  = Carbon::now()->format('Ymd');
        $prefix = 'ADJ-' . $today . '-';

        $lastAdjustment = static::where('adjustment_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastAdjustment) {
            $lastNumber = (int) substr($lastAdjustment->adjustment_code, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * User (admin) yang melakukan penyesuaian stok.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Produk yang stoknya disesuaikan.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: filter penyesuaian yang menambah stok (difference > 0).
     */
    public function scopePositive(Builder $query): Builder
    {
        return $query->where('difference', '>', 0);
    }

    /**
     * Scope: filter penyesuaian yang mengurangi stok (difference < 0).
     */
    public function scopeNegative(Builder $query): Builder
    {
        return $query->where('difference', '<', 0);
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
     * Label alasan penyesuaian dalam Bahasa Indonesia.
     */
    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? ucfirst(str_replace('_', ' ', $this->reason));
    }

    /**
     * Tampilkan difference dengan tanda + atau -.
     */
    public function getFormattedDifferenceAttribute(): string
    {
        if ($this->difference > 0) {
            return '+' . $this->difference;
        }

        return (string) $this->difference;
    }

    /**
     * Cek apakah penyesuaian ini menambah stok.
     */
    public function isPositive(): bool
    {
        return $this->difference > 0;
    }

    /**
     * Cek apakah penyesuaian ini mengurangi stok.
     */
    public function isNegative(): bool
    {
        return $this->difference < 0;
    }
}
