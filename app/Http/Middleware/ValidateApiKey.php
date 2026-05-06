<?php

namespace App\Http\Middleware;

use App\Models\ApiConsumer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $consumer = ApiConsumer::findForPlainKey($request->header('X-API-KEY'));

        if (! $consumer || ! $consumer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401);
        }

        $cacheKey = sprintf('api-consumer:%d:%s', $consumer->id, now()->format('YmdHi'));
        $hits = Cache::increment($cacheKey);
        Cache::put($cacheKey, $hits, now()->addMinute());

        if ($hits > $consumer->rate_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded',
            ], 429);
        }

        $consumer->forceFill(['last_used_at' => now()])->save();
        $request->attributes->set('api_consumer', $consumer);

        return $next($request);
    }
}
