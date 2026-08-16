<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
        'group',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Cache key prefix untuk setting.
     */
    private const CACHE_PREFIX = 'setting_';

    /**
     * Cache duration (seconds) — 60 menit.
     */
    private const CACHE_TTL = 3600;

    // =========================================================================
    // STATIC HELPERS
    // =========================================================================

    /**
     * Ambil nilai setting berdasarkan key.
     * Menggunakan cache untuk performa optimal.
     *
     * @param  string  $key     Key setting
     * @param  mixed   $default Nilai default jika key tidak ditemukan
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX . $key,
            self::CACHE_TTL,
            function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            }
        );
    }

    /**
     * Set atau update nilai setting berdasarkan key.
     * Otomatis membersihkan cache setelah update.
     *
     * @param  string  $key   Key setting
     * @param  mixed   $value Nilai baru
     * @return bool
     */
    public static function set(string $key, mixed $value): bool
    {
        $updated = static::where('key', $key)->update(['value' => $value]);

        if ($updated) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        return (bool) $updated;
    }

    /**
     * Ambil semua setting dalam satu group sebagai array asosiatif.
     *
     * @param  string  $group  Nama group
     * @return array<string, mixed>
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'group_' . $group,
            self::CACHE_TTL,
            function () use ($group) {
                return static::where('group', $group)
                    ->pluck('value', 'key')
                    ->toArray();
            }
        );
    }

    /**
     * Ambil semua setting sebagai array asosiatif key => value.
     *
     * @return array<string, mixed>
     */
    public static function getAllAsArray(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'all',
            self::CACHE_TTL,
            function () {
                return static::pluck('value', 'key')->toArray();
            }
        );
    }

    /**
     * Bersihkan semua cache setting.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key')->toArray();

        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        Cache::forget(self::CACHE_PREFIX . 'all');

        $groups = static::distinct()->pluck('group')->toArray();
        foreach ($groups as $group) {
            Cache::forget(self::CACHE_PREFIX . 'group_' . $group);
        }
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Konversi nilai ke tipe yang sesuai berdasarkan type field.
     */
    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number'  => is_numeric($this->value) ? (float) $this->value : 0,
            default   => $this->value,
        };
    }
}
