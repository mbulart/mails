<?php

namespace App\Models;

use App\Enums\MailLogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_consumer_id',
        'mail_type_id',
        'recipient',
        'subject',
        'payload',
        'status',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status' => MailLogStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    public function apiConsumer(): BelongsTo
    {
        return $this->belongsTo(ApiConsumer::class);
    }

    public function mailType(): BelongsTo
    {
        return $this->belongsTo(MailType::class);
    }
}
