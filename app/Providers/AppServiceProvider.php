<?php

namespace App\Providers;

use DateTime;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $uuid = config('app.uuid');

        if (!$uuid) {
            $uuid = $this->registerUuid();
        }

        $this->registerLogListeners($uuid);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Register Uuid for tracking events log
     *
     * @return string
     */
    protected function registerUuid(): string
    {
        $uuid = (string) Str::uuid();
        config(['app.uuid' => $uuid]);
        return $uuid;
    }

    /**
     * Register log listeners for request and response events.
     *
     * @param string $uuid
     * @return void
     */
    protected function registerLogListeners(string $uuid): void
    {
        DB::listen(function ($query) use ($uuid) {
            $this->logSql($query, $uuid);
        });

        Event::listen(function (RouteMatched $event) use ($uuid) {
            $this->logRequest($event, $uuid);
        });

        Event::listen(function (RequestHandled $event) use ($uuid) {
            $this->logResponse($event, $uuid);
        });
    }

    /**
     * Log SQL query.
     *
     * @param mixed $query
     * @param string $uuid
     * @return void
     */
    protected function logSql($query, string $uuid): void
    {
        if ($query->sql instanceof Expression) {
            $rawSql = $query->sql->getValue();
        } else {
            $rawSql = $query->sql;
        }

        if (strlen($rawSql) > 10000) {
            $rawSql = substr($rawSql, 0, 10000) . '...';
        }

        if (strpos($rawSql, 'select * from `jobs`') === false) {
            Log::info('SQL', [
                'uuid' => $uuid,
                'Sql' => array_reduce($query->bindings, function ($sql, $binding) {
                    if ($binding instanceof DateTime) {
                        $binding = $binding->format('Y-m-d H:i:s');
                    }
                    return preg_replace('/\?/', is_numeric($binding) ? $binding : "'{$binding}'", $sql, 1);
                }, $rawSql),
                'Timing' => $query->time,
            ]);
        }
    }

    /**
     * Log incoming request.
     *
     * @param RouteMatched $event
     * @param string $uuid
     * @return void
     */
    protected function logRequest(RouteMatched $event, string $uuid): void
    {
        $request = $event->request;
        Log::info(
            'REQUEST',
            [
                'uuid' => $uuid,
                'url' => $request->fullUrl(),
                'ip' => $request->ips(),
                'headers' => $request->header(),
                'params' => $request->all(),
                'method' => $request->method(),
            ]
        );
    }

    /**
     * Log API response.
     *
     * @param RequestHandled $event
     * @param string $uuid
     * @return void
     */
    protected function logResponse(RequestHandled $event, string $uuid): void
    {
        $response = $event->response;
        if ($response instanceof JsonResponse && $response->getStatusCode() !== Response::HTTP_OK) {
            Log::info(
                'RESPONSE',
                [
                    'uuid' => $uuid,
                    'headers' => $response->headers->all(),
                    'status' => $response->status(),
                    'data' => $response->getData(),
                ],
            );
        }
    }
}
