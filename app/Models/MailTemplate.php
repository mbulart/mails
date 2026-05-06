<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_type_id',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function mailType(): BelongsTo
    {
        return $this->belongsTo(MailType::class);
    }
}
