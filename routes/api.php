<?php

use App\Http\Controllers\Api\SendMailController;
use App\Http\Middleware\ValidateApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(ValidateApiKey::class)->group(function (): void {
    Route::post('/mails/send', SendMailController::class);
});
