<?php

namespace App\Http\Middleware;

use App\Exceptions\TenantResolutionException;
use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Must run before anything that touches the database, session or storage.
 */
class IdentifyTenant
{
    public function __construct(private readonly TenantManager $tenancy) {}

        public function handle(Request $request, Closure $next): Response
    {
        if (! $this->tenancy->enabled()) {
            return $next($request);
        }

        // 1. Resolve tenant via Bearer token (Sanctum) if present
        if ($bearerToken = $request->bearerToken()) {
            try {
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($bearerToken);
                if ($tokenModel && $tokenModel->tokenable) {
                    $user = $tokenModel->tokenable;
                    \Illuminate\Support\Facades\Auth::setUser($user);
                    if (!empty($user->email)) {
                        $forceFresh = $request->has('refresh_tenant') || $request->has('refresh_permissions');
                        $tenant = $this->tenancy->resolveByEmail($user->email, $forceFresh);
                        if ($request->hasSession()) {
                            $request->session()->put('tenant_id', $tenant['id']);
                            $request->session()->put('tenant', $tenant);
                        }
                        return $next($request);
                    }
                }
            } catch (TenantResolutionException $e) {
                return $this->deny($request, $e);
            } catch (\Throwable $e) {
                // fallback to other checks
            }
        }

        // 2. If a tenant session already exists, resolve and activate that tenant
        if ($request->hasSession() && $request->session()->has('tenant_id')) {
            try {
                $tenantId = (int) $request->session()->get('tenant_id');
                $forceFresh = $request->has('refresh_tenant') || $request->has('refresh_permissions');

                $tenant = $this->tenancy->resolveById($tenantId, $forceFresh);
                $request->session()->put('tenant', $tenant);

                return $next($request);
            } catch (TenantResolutionException $e) {
                // If remote admin_erp is temporarily unreachable, fallback to cached session tenant if valid
                $cachedTenant = $request->session()->get('tenant');
                if (is_array($cachedTenant) && !empty($cachedTenant['db']['database']) && $e->reason === 'UNREACHABLE') {
                    try {
                        $this->tenancy->activate($cachedTenant);
                        return $next($request);
                    } catch (\Throwable) {
                        // proceed to deny
                    }
                }

                // If tenant is expired/inactive, logout and deny
                if ($request->hasSession()) {
                    $request->session()->forget(['tenant_id', 'tenant']);
                }
                \Illuminate\Support\Facades\Auth::logout();
                return $this->deny($request, $e);
            }
        }

        // 3. If user is authenticated via session cookie or access_token in session
        if ($request->hasSession()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user && $request->session()->has('user_id')) {
                $user = \App\Models\User::find($request->session()->get('user_id'));
                if ($user) {
                    \Illuminate\Support\Facades\Auth::setUser($user);
                }
            }

            if ($user && !empty($user->email)) {
                try {
                    $forceFresh = $request->has('refresh_tenant') || $request->has('refresh_permissions');
                    $tenant = $this->tenancy->resolveByEmail($user->email, $forceFresh);
                    $request->session()->put('tenant_id', $tenant['id']);
                    $request->session()->put('tenant', $tenant);

                    return $next($request);
                } catch (TenantResolutionException $e) {
                    return $this->deny($request, $e);
                } catch (\Throwable) {
                    \Illuminate\Support\Facades\Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }
        }

        // 4. If request is on central domain or localhost and not yet authenticated, handle public endpoints
        $centralHost = static::isCentralHost($request->getHost());

        // For public login, logout, and password recovery endpoints, allow passing through
        if (
            $request->is('/') ||
            $request->is('login') ||
            $request->is('logout') ||
            $request->is('api/login*') ||
            $request->is('api/loginweb') ||
            $request->is('forgot-password') ||
            $request->is('reset-password*') ||
            $request->is('storage/tenants/*') ||
            $request->is('storage/*')
        ) {
            if (! $centralHost) {
                // If on a client subdomain, resolve that client's branding/theme
                try {
                    $this->tenancy->resolveByHost($request->getHost());
                } catch (TenantResolutionException $e) {
                    // Ignore on public signin
                }
            }
            return $next($request);
        }

        // 5. On central domain without a tenant session, check auth before redirect/denial
        if ($centralHost) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user && $request->bearerToken()) {
                $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            }

            if ($user && !empty($user->email)) {
                try {
                    $tenant = $this->tenancy->resolveByEmail($user->email);
                    if ($request->hasSession()) {
                        $request->session()->put('tenant_id', $tenant['id']);
                        $request->session()->put('tenant', $tenant);
                    }
                    return $next($request);
                } catch (TenantResolutionException $e) {
                    return $this->deny($request, $e);
                } catch (\Throwable) {
                    // proceed to deny/unauthenticated
                }
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['status' => false, 'error' => 'Unauthenticated'], 401);
            }
            if (! $request->is('/') && ! $request->is('login')) {
                return redirect()->route('login');
            }
            return $next($request);
        }

        // 6. Otherwise attempt domain resolution (subdomain-based access)
        try {
            $this->tenancy->resolveByHost($request->getHost());
        } catch (TenantResolutionException $e) {
            return $this->deny($request, $e);
        }

        return $next($request);
    }

    private function deny(Request $request, TenantResolutionException $e): Response
    {
        $status = in_array($e->status, [401, 403, 404, 409, 500, 503], true) ? $e->status : 403;

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => false,
                'success' => false,
                'tenant_error' => $e->reason,
                'error' => $e->getMessage(),
                'message' => $e->getMessage(),
                'expiry_date' => $e->context['expiry_date'] ?? null,
            ], $status);
        }

        return response()->view('errors.tenant', [
            'reason' => $e->reason,
            'message' => $e->getMessage(),
            'expiryDate' => $e->context['expiry_date'] ?? null,
        ], $status);
    }

    public static function isCentralHost(string $host): bool
    {
        $host = strtolower(trim($host));
        $host = preg_replace('#:\d+$#', '', $host);

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if ($appHost && strtolower($appHost) === $host) {
            return true;
        }

        return in_array($host, [
            'localhost',
            '127.0.0.1',
            'hms-demo.fableadtech.com',
            'devhms.fableadtech.com',
            'hms.fableadtech.com',
        ], true);
    }
}

