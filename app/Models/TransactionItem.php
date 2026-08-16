<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'product_code',
        'unit',
        'selling_price',
        'purchase_price',
        'quantity',
        'discount_per_item',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'selling_price'    => 'decimal:2',
        'purchase_price'   => 'decimal:2',
        'quantity'         => 'integer',
        'discount_per_item'=> 'decimal:2',
        'subtotal'         => 'decimal:2',
    ];

    // =========================================================================
    // BOOT
    // =========================================================================

    /**
     * Boot method: auto-calculate subtotal sebelum menyimpan.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TransactionItem $item) {
            // Subtotal = (harga jual - diskon per item) * qty
            $effectivePrice = (float) $item->selling_price - (float) $item->discount_per_item;
            $item->subtotal = max(0, $effectivePrice * $item->quantity);
        });

        static::updating(function (TransactionItem $item) {
            $effectivePrice = (float) $item->selling_price - (float) $item->discount_per_item;
            $item->subtotal = max(0, $effectivePrice * $item->quantity);
        });
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Transaksi induk item ini.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Referensi ke produk asli (bisa null jika produk dihapus).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // =========================================================================
    // ACCESSORS / HELPERS
    // =========================================================================

    /**
     * Hitung profit per item (nominal).
     */
    public function getProfitPerItemAttribute(): float
    {
        return (float) $this->selling_price - (float) $this->purchase_price - (float) $this->discount_per_item;
    }

    /**
     * Total profit item ini (profit_per_item * quantity).
     */
    public function getTotalProfitAttribute(): float
    {
        return $this->profit_per_item * $this->quantity;
    }

    /**
     * Harga efektif setelah diskon per item.
     */
    public function getEffectivePriceAttribute(): float
    {
        return max(0, (float) $this->selling_price - (float) $this->discount_per_item);
    }
}
