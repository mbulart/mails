<?php

use App\Jobs\SendMailJob;
use App\Models\ApiConsumer;
use App\Models\MailTemplate;
use App\Models\MailType;
use App\Services\TemplateRenderer;
use Illuminate\Support\Facades\Queue;

it('rejects requests without a valid api key', function (): void {
    $this->postJson('/api/v1/mails/send', [])->assertUnauthorized()->assertJson([
        'success' => false,
        'message' => 'Invalid API key',
    ]);
});

it('queues a templated email with a valid api key', function (): void {
    Queue::fake();

    $apiKey = ApiConsumer::makeApiKey();
    $consumer = ApiConsumer::query()->create([
        'name' => 'Test app',
        'email' => 'app@example.com',
        'api_key_hash' => $apiKey['hash'],
        'api_key_preview' => $apiKey['preview'],
        'is_active' => true,
        'rate_limit' => 100,
    ]);

    $type = MailType::query()->create([
        'name' => 'OTP',
        'slug' => 'otp_code',
        'is_active' => true,
    ]);

    MailTemplate::query()->create([
        'mail_type_id' => $type->id,
        'subject' => 'Code {{otp}}',
        'html_content' => '<p>Bonjour {{name}}, code {{otp}}</p>',
        'variables' => [['name' => 'name'], ['name' => 'otp']],
        'is_default' => true,
        'is_active' => true,
    ]);

    $this->withHeader('X-API-KEY', $apiKey['plain'])
        ->postJson('/api/v1/mails/send', [
            'type' => 'otp_code',
            'to' => ['client@example.com'],
            'variables' => ['name' => 'Joseph', 'otp' => '123456'],
        ])
        ->assertAccepted()
        ->assertJson(['success' => true]);

    Queue::assertPushed(SendMailJob::class);
    expect($consumer->fresh()->last_used_at)->not->toBeNull();
});

it('renders simple variables and blade control structures', function (): void {
    $html = app(TemplateRenderer::class)->render(
        '<p>{{name}}</p>@if($premium)<strong>Premium</strong>@endif',
        ['name' => 'Joseph', 'premium' => true],
    );

    expect($html)->toContain('Joseph')->toContain('Premium');
});

it('rotates api keys without storing the plain value', function (): void {
    $key = ApiConsumer::makeApiKey();
    $consumer = ApiConsumer::query()->create([
        'name' => 'Rotate me',
        'email' => 'rotate@example.com',
        'api_key_hash' => $key['hash'],
        'api_key_preview' => $key['preview'],
        'is_active' => true,
        'rate_limit' => 100,
    ]);

    $newPlainKey = $consumer->rotateApiKey();

    expect($newPlainKey)->toStartWith('pmk_')
        ->and($consumer->fresh()->api_key_hash)->toBe(hash('sha256', $newPlainKey))
        ->and($consumer->fresh()->api_key_hash)->not->toBe($newPlainKey);
});
