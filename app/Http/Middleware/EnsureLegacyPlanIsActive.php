<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegacyPlanIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->resolveUser($request);

        if (! $user instanceof User) {
            return $this->deny($request, 'Usuario no autenticado.');
        }

        if ($user->getAttribute('is_approved') === null) {
            return $next($request);
        }

        if (! $user->is_approved) {
            return $this->deny($request, 'Tu cuenta aún no ha sido aprobada.');
        }

        if ($user->getAttribute('is_subscribed') === null) {
            return $next($request);
        }

        if (! $user->is_subscribed) {
            return $this->deny($request, 'Tu plan de suscripción no está activo.');
        }

        return $next($request);
    }

    private function resolveUser(Request $request): ?User
    {
        $user = $request->user();

        if ($user instanceof User) {
            return $user;
        }

        return auth()->user();
    }

    private function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return new JsonResponse(['message' => $message], Response::HTTP_PAYMENT_REQUIRED);
        }

        return redirect()
            ->route('login', ['auth_required' => 1])
            ->with('auth.status', [
                'type' => 'warning',
                'message' => $message,
            ]);
    }
}
