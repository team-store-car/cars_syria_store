<?php
namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        return $next($request);
    }
}
