<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSlowQueries
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enable query logging only in development/testing
        if (config('app.debug')) {
            $startTime = microtime(true);

            // Count queries before
            $queryCount = count(DB::getQueryLog());

            // Enable query logging
            DB::enableQueryLog();

            $response = $next($request);

            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Convert to ms

            // Log slow requests (> 500ms)
            if ($executionTime > 500) {
                $queries = DB::getQueryLog();
                Log::warning('Slow API Request', [
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'duration_ms' => $executionTime,
                    'query_count' => count($queries),
                ]);

                // Log top 5 slowest queries
                foreach (array_slice($queries, -5) as $query) {
                    if ($query['time'] > 100) { // Log queries > 100ms
                        Log::debug('Slow Query', [
                            'query' => $query['query'],
                            'bindings' => $query['bindings'],
                            'time_ms' => $query['time'],
                        ]);
                    }
                }
            }

            return $response;
        }

        return $next($request);
    }
}
