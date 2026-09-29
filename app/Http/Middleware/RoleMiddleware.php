<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /** Vérifie que l'utilisateur connecté possède le rôle attendu. */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== $role) {
            return response()->json([
                'message' => 'Accès non autorisé pour ce compte.',
            ], 403);
        }

        return $next($request);
    }
}
