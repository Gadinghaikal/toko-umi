<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_number',
        'user_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'amount_paid',
        'change_amount',
        'payment_method',
        'status',
        'notes',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal'         => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'grand_total'      => 'decimal:2',
        'amount_paid'      => 'decimal:2',
        'change_amount'    => 'decimal:2',
        'transaction_date' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Metode pembayaran yang tersedia.
     */
    public const PAYMENT_METHODS = [
        'tunai'    => 'Tunai',
        'transfer' => 'Transfer Bank',
        'qris'     => 'QRIS',
    ];

    // =========================================================================
    // BOOT
    // =========================================================================

    /**
     * Boot method: auto-generate nomor invoice dan set transaction_date.
     * Format Invoice: INV-YYYYMMDD-XXX (contoh: INV-20240101-001)
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Transaction $transaction) {
            if (empty($transaction->invoice_number)) {
                $transaction->invoice_number = static::generateInvoiceNumber();
            }

            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = Carbon::now();
            }

            // Hitung change_amount otomatis
            if ($transaction->amount_paid > 0 && $transaction->change_amount == 0) {
                $transaction->change_amount = max(0, (float) $transaction->amount_paid - (float) $transaction->grand_total);
            }
        });
    }

    /**
     * Generate nomor invoice otomatis.
     * Format: INV-YYYYMMDD-XXX
     */
    public static function generateInvoiceNumber(): string
    {
        $today  = Carbon::now()->format('Ymd');
        $prefix = 'INV-' . $today . '-';

        $lastTransaction = static::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->invoice_number, -3);
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
     * Kasir yang memproses transaksi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Detail item-item dalam transaksi ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: filter hanya transaksi yang completed.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: filter hanya transaksi yang cancelled.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope: filter transaksi hari ini.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('transaction_date', Carbon::today());
    }

    /**
     * Scope: filter transaksi bulan ini.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereYear('transaction_date', Carbon::now()->year)
                     ->whereMonth('transaction_date', Carbon::now()->month);
    }

    /**
     * Scope: filter berdasarkan rentang tanggal.
     */
    public function scopeDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('transaction_date', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59',
        ]);
    }

    /**
     * Scope: filter berdasarkan metode pembayaran.
     */
    public function scopePaymentMethod(Builder $query, string $method): Builder
    {
        return $query->where('payment_method', $method);
    }

    // =========================================================================
    // ACCESSORS / HELPERS
    // =========================================================================

    /**
     * Label metode pembayaran dalam Bahasa Indonesia.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    /**
     * Status label dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }

    /**
     * Badge color Bootstrap berdasarkan status.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }

    /**
     * Cek apakah transaksi ini selesai.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Cek apakah transaksi ini dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Hitung total profit transaksi ini berdasarkan item-item.
     */
    public function getTotalProfitAttribute(): float
    {
        return $this->items->sum(function (TransactionItem $item) {
            return ((float) $item->selling_price - (float) $item->purchase_price) * $item->quantity;
        });
    }

    /**
     * Jumlah total item (quantity) dalam transaksi ini.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
