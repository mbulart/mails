<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiConsumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'api_key_hash',
        'api_key_preview',
        'is_active',
        'rate_limit',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'rate_limit' => 'integer',
        ];
    }

    public static function generatePlainApiKey(): string
    {
        return 'pmk_'.Str::random(48);
    }

    /**
     * @return array{plain:string, hash:string, preview:string}
     */
    public static function makeApiKey(): array
    {
        $plain = self::generatePlainApiKey();

        return [
            'plain' => $plain,
            'hash' => hash('sha256', $plain),
            'preview' => substr($plain, 0, 8).'...',
        ];
    }

    public static function findForPlainKey(?string $plainKey): ?self
    {
        if (blank($plainKey)) {
            return null;
        }

        return self::query()
            ->where('api_key_hash', hash('sha256', $plainKey))
            ->first();
    }

    public function rotateApiKey(): string
    {
        $key = self::makeApiKey();

        $this->forceFill([
            'api_key_hash' => $key['hash'],
            'api_key_preview' => $key['preview'],
        ])->save();

        return $key['plain'];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MailLog::class);
    }
}
