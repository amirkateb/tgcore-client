<?php

namespace amirkateb\TGCoreClient\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use amirkateb\TGCoreClient\Contracts\BotSecretResolver;
use Symfony\Component\HttpFoundation\Response;

class VerifyTGCoreSignature
{
    public function __construct(
        private readonly BotSecretResolver $resolver
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $raw = (string) $request->getContent();

        $ts = (string) $request->header('X-TGCore-Timestamp', '');
        $sig = (string) $request->header('X-TGCore-Signature', '');
        $botUuid = (string) $request->header('X-TGCore-Bot-UUID', '');

        if ($ts === '' || $sig === '' || $botUuid === '') {
            return response()->json(['ok' => false, 'error' => 'missing_headers'], 401);
        }

        if (!ctype_digit($ts)) {
            return response()->json(['ok' => false, 'error' => 'invalid_timestamp'], 401);
        }

        $tsInt = (int) $ts;
        $tol = (int) config('tgcore_client.signature.tolerance_seconds', 300);

        if (abs(time() - $tsInt) > $tol) {
            return response()->json(['ok' => false, 'error' => 'timestamp_out_of_range'], 401);
        }

        $secret = $this->resolver->resolve($botUuid, []);
        if (!is_string($secret) || $secret === '') {
            return response()->json(['ok' => false, 'error' => 'unknown_bot'], 401);
        }

        $expected = 'sha256=' . hash_hmac('sha256', $tsInt . "\n" . $raw, $secret);

        if (!hash_equals($expected, $sig)) {
            return response()->json(['ok' => false, 'error' => 'invalid_signature'], 401);
        }

        $ttl = (int) config('tgcore_client.signature.replay_ttl_seconds', 600);
        $replayKey = 'tgcore_client:replay:' . $botUuid . ':' . hash('sha256', $sig);

        if (Cache::has($replayKey)) {
            return response()->json(['ok' => true, 'duplicate' => true], 200);
        }

        Cache::put($replayKey, 1, $ttl);

        $request->attributes->set('tgcore_client.bot_uuid', $botUuid);

        return $next($request);
    }
}
