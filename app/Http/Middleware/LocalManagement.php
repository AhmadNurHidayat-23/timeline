<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalManagement
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use the connection address, never a client-controlled forwarding header.
        abort_unless(in_array($request->server('REMOTE_ADDR'), [
            '127.0.0.1', '::1', '::ffff:127.0.0.1',
        ], true), 403, 'Pengelolaan Timeline hanya tersedia melalui komputer lokal.');

        return $next($request);
    }
}
