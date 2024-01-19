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
        $this->registerUuid();
        $uuid = config('app.uuid');
        if (!$uuid) {
            $uuid = (string)Str::uuid();
        }
        Log::withContext(['uuid' => config('app.uuid'), 'path' => request()->path()]);

        DB::listen(function ($query) use ($uuid) {
            if ($query->sql instanceof Expression) {
                $rawSql = $query->sql->getValue();
            } else {
                $rawSql = $query->sql;
            }
            if (strlen($rawSql) > 10000) {
                $rawSql = substr($rawSql, 0, 10000) . '...';
            }
            // Exclude queries related to the jobs table
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
        });

        // Incoming request
        Event::listen(function (RouteMatched $event) use ($uuid) {
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
        });

        // Api response
        Event::listen(function (RequestHandled $event) use ($uuid) {
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
        });
    }

    /**
     * Register Uuid for tracking events log
     *
     * @return void
     */
    protected function registerUuid(): void
    {
        $uuid = (string)Str::uuid();
        config(['app.uuid' => $uuid]);
    }
}
