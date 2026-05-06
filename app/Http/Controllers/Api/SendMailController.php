<?php

namespace App\Http\Controllers\Api;

use App\DTOs\MailSendData;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMailRequest;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class SendMailController extends Controller
{
    public function __invoke(SendMailRequest $request, MailService $mailService): JsonResponse
    {
        try {
            $logIds = $mailService->queue(
                MailSendData::fromArray($request->validated()),
                $request->attributes->get('api_consumer'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Mail queued successfully',
                'log_ids' => $logIds,
            ], 202);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
