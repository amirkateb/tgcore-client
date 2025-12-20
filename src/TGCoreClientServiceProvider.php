<?php

namespace amirkateb\TGCoreClient;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use amirkateb\TGCoreClient\Contracts\BotSecretResolver;
use amirkateb\TGCoreClient\Dispatcher\HandlerRegistry;
use amirkateb\TGCoreClient\Dispatcher\TGCoreDispatcher;
use amirkateb\TGCoreClient\Gateway\TGCoreGatewayClient;
use amirkateb\TGCoreClient\Http\Middleware\VerifyTGCoreSignature;
use amirkateb\TGCoreClient\Resolvers\ConfigBotSecretResolver;
use amirkateb\TGCoreClient\Resolvers\DatabaseBotSecretResolver;

class TGCoreClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tgcore_client.php', 'tgcore_client');

        $this->app->singleton(BotSecretResolver::class, function () {
            $mode = (string) config('tgcore_client.resolver', 'config');

            if ($mode === 'database') {
                return $this->app->make(DatabaseBotSecretResolver::class);
            }

            return $this->app->make(ConfigBotSecretResolver::class);
        });

        $this->app->singleton(HandlerRegistry::class, fn () => new HandlerRegistry());
        $this->app->singleton(TGCoreDispatcher::class, fn () => new TGCoreDispatcher($this->app->make(HandlerRegistry::class)));
        $this->app->singleton(TGCoreGatewayClient::class, fn () => new TGCoreGatewayClient($this->app->make(BotSecretResolver::class)));
    }

    public function boot(Router $router): void
    {
        $router->aliasMiddleware('tgcore-client.verify', VerifyTGCoreSignature::class);

        RateLimiter::for('tgcore-ingest', function ($request) {
            $limit = (int) config('tgcore_client.rate_limits.ingest_per_minute', 120);
            if ($limit <= 0) {
                return Limit::none();
            }

            $botUuid = (string) $request->header('X-TGCore-Bot-UUID', 'unknown');
            $ip = (string) ($request->ip() ?? 'unknown');

            return Limit::perMinute($limit)->by('tgcore:ingest:' . $botUuid . ':' . $ip);
        });

        RateLimiter::for('tgcore-gateway', function ($request) {
            $limit = (int) config('tgcore_client.rate_limits.gateway_per_minute', 120);
            if ($limit <= 0) {
                return Limit::none();
            }

            $botUuid = (string) $request->header('X-TGCore-Bot-UUID', 'unknown');
            $ip = (string) ($request->ip() ?? 'unknown');

            return Limit::perMinute($limit)->by('tgcore:gateway:' . $botUuid . ':' . $ip);
        });

        $this->loadRoutesFrom(__DIR__ . '/../routes/tgcore-client.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/tgcore_client.php' => config_path('tgcore_client.php'),
        ], 'tgcore-client-config');
    }
}