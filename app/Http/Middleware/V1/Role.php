<?php

namespace App\Http\Middleware\V1;

use Illuminate\Http\Request;
use App\Traits\V1\ApiResponses;
use Closure;

class Role
{
    use ApiResponses;

    public function handle(Request $request, Closure $next, mixed ...$roles)
    {
        if (!$request->user() || !$request->user()->hasRole($roles)) {
            return $this->error('Unauthorized action', 403);
        }

        return $next($request);
    }
}
