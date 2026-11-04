<?php

namespace App\Services\Tenancy;

use App\Exceptions\TenantResolutionException;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Resolves the incoming request to a client record managed by the central
 * admin_erp panel and points the database, storage, cache and
 * session at that client's own resources.
 */
class TenantManager
{
    private ?array $tenant = null;

    private string $sharedStoragePath;

    private ?string $baseSessionCookie = null;

    private ?Repository $lookupCache = null;

    public function __construct()
    {
        $this->sharedStoragePath = storage_path();
    }

    public function enabled(): bool
    {
        return (bool) config('tenant.enabled');
    }

    public function current(): ?array
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return isset($this->tenant['id']) ? (int) $this->tenant['id'] : null;
    }

    public function sharedStoragePath(string $path = ''): string
    {
        return $this->sharedStoragePath . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '\\/') : '');
    }

    public static function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));
        $host = preg_replace('#^[a-z][a-z0-9+.-]*://#', '', $host);
        $host = preg_replace('#[/?\#].*$#', '', $host);
        $host = preg_replace('#:\d+$#', '', $host);

        return preg_replace('#^www\.#', '', $host);
    }

    /**
     * @param bool $fresh skip the short-lived cache (used at login so suspensions apply immediately)
     */
    public function resolveByHost(string $host, bool $fresh = false): array
    {
        $host = static::normalizeHost($host);
        $key = 'host:' . sha1($host . '|' . config('tenant.product_code') . '|' . config('tenant.project_code'));
        $cache = $this->lookupCache();

        if (! $fresh && is_array($cached = $cache->get($key))) {
            return $this->activate($cached);
        }

        try {
            $response = $this->client()->post('/resolve', [
                'domain' => $host,
                'product_code' => config('tenant.product_code'),
                'project_code' => config('tenant.project_code') ?: null,
            ]);
        } catch (ConnectionException $e) {
            return $this->activateStaleOrFail($key, $e->getMessage());
        }

        if ($response->serverError()) {
            return $this->activateStaleOrFail($key, 'HTTP ' . $response->status());
        }

        $tenant = $this->tenantFromResponse($response, $key);

        $cache->put($key, $tenant, max(1, (int) config('tenant.cache_seconds')));
        $cache->put($key . ':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));

        return $this->activate($tenant);
    }

    /**
     * Resolve tenant by user email / username (for central login).
     */
    public function resolveByEmail(string $email, bool $fresh = false): array
    {
        $email = trim(strtolower($email));
        $key = 'user_email:' . sha1($email . '|' . config('tenant.product_code') . '|' . config('tenant.project_code'));
        $cache = $this->lookupCache();

        if (! $fresh && is_array($cached = $cache->get($key))) {
            return $this->activate($cached);
        }

        try {
            $response = $this->client()->post('/resolve-user', array_filter([
                'email' => $email,
                'username' => $email,
                'login' => $email,
                'product_code' => config('tenant.product_code'),
                'project_code' => config('tenant.project_code') ?: null,
            ]));
        } catch (ConnectionException $e) {
            return $this->activateStaleOrFail($key, $e->getMessage());
        }

        if ($response->serverError()) {
            return $this->activateStaleOrFail($key, 'HTTP ' . $response->status());
        }

        $tenant = $this->tenantFromResponse($response, $key);

        $cache->put($key, $tenant, max(1, (int) config('tenant.cache_seconds')));
        $cache->put($key . ':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));

        return $this->activate($tenant);
    }

    /**
     * Every client database of this product/project (for console commands).
     *
     * @return array<int, array>
     */
    public function all(?int $tenantId = null): array
    {
        try {
            $response = $this->client()->get('/list', array_filter([
                'product_code' => config('tenant.product_code'),
                'project_code' => config('tenant.project_code') ?: null,
                'tenant_id' => $tenantId,
            ]));
        } catch (ConnectionException $e) {
            throw new TenantResolutionException('UNREACHABLE', 'Unable to reach the admin_erp panel: ' . $e->getMessage(), 503);
        }

        if (! $response->successful()) {
            throw new TenantResolutionException(
                'LIST_FAILED',
                (string) ($response->json('message') ?? 'Tenant list request failed (HTTP ' . $response->status() . ').'),
                $response->status()
            );
        }

        return array_values(array_filter(
            (array) $response->json('tenants', []),
            fn ($tenant) => $this->belongsToThisApp($tenant)
        ));
    }

    public function activateById(int $tenantId, bool $fresh = false): array
    {
        return $this->resolveById($tenantId, $fresh);
    }

    /**
     * Resolve and activate tenant by setup/tenant ID.
     */
    public function resolveById(int $tenantId, bool $fresh = false): array
    {
        $key = 'tenant_id:' . (int) $tenantId . '|' . config('tenant.product_code') . '|' . config('tenant.project_code');
        $cache = $this->lookupCache();

        if (! $fresh && is_array($cached = $cache->get($key))) {
            return $this->activate($cached);
        }

        try {
            $tenants = $this->all($tenantId);
            $tenant = $tenants[0] ?? null;
        } catch (ConnectionException $e) {
            return $this->activateStaleOrFail($key, $e->getMessage());
        }

        if (! $tenant) {
            $this->lookupCache()->forget($key);
            $this->lookupCache()->forget($key . ':stale');
            throw new TenantResolutionException('NOT_FOUND', "Tenant #{$tenantId} was not found for this product.", 404);
        }

        if (! $this->isUsable($tenant)) {
            $this->lookupCache()->forget($key);
            $this->lookupCache()->forget($key . ':stale');
            $reason = ($tenant['membership_status'] ?? '') === 'Expired' ? 'EXPIRED' : 'INACTIVE';
            throw new TenantResolutionException($reason, "Tenant #{$tenantId} is currently not active or subscription has expired.", 403, [
                'expiry_date' => $tenant['expiry_date'] ?? null,
                'plan_name' => $tenant['plan_name'] ?? 'Starter',
            ]);
        }

        $cache->put($key, $tenant, max(1, (int) config('tenant.cache_seconds')));
        $cache->put($key . ':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));

        return $this->activate($tenant);
    }

    /**
     * Synchronize client admin profile modifications to master admin_erp database.
     */
    public function syncProfileToAdmin(array $payload): ?Response
    {
        try {
            $response = $this->client()->post('/update-profile', array_filter([
                'client_id' => $payload['client_id'] ?? null,
                'tenant_id' => $payload['tenant_id'] ?? null,
                'domain' => $payload['domain'] ?? null,
                'current_email' => $payload['current_email'] ?? null,
                'name' => $payload['name'] ?? null,
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'password' => $payload['password'] ?? null,
            ]));

            // Invalidate local tenant lookup cache so fresh details are picked up immediately
            $this->forgetCurrentCache();

            return $response;
        } catch (\Throwable $e) {
            Log::warning('TenantManager syncProfileToAdmin error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear all cached lookup entries for the current tenant.
     */
    public function forgetCurrentCache(): void
    {
        try {
            $cache = $this->lookupCache();
            if ($this->tenant) {
                if (!empty($this->tenant['domain'])) {
                    $host = static::normalizeHost($this->tenant['domain']);
                    $key = 'host:' . sha1($host . '|' . config('tenant.product_code') . '|' . config('tenant.project_code'));
                    $cache->forget($key);
                    $cache->forget($key . ':stale');
                }
                if (!empty($this->tenant['email'])) {
                    $email = trim(strtolower($this->tenant['email']));
                    $key = 'user_email:' . sha1($email . '|' . config('tenant.product_code') . '|' . config('tenant.project_code'));
                    $cache->forget($key);
                    $cache->forget($key . ':stale');
                }
                if (!empty($this->tenant['id'])) {
                    $key = 'tenant_id:' . (int) $this->tenant['id'] . '|' . config('tenant.product_code') . '|' . config('tenant.project_code');
                    $cache->forget($key);
                    $cache->forget($key . ':stale');
                }
            }
        } catch (\Throwable) {
            // Ignore cache forget errors
        }
    }

    public function isUsable(array $tenant): bool
    {
        return strcasecmp((string) ($tenant['status'] ?? ''), 'Active') === 0
            && strcasecmp((string) ($tenant['client_status'] ?? 'Active'), 'Active') === 0
            && ! in_array($tenant['membership_status'] ?? null, ['Expired', 'Pending'], true);
    }

        public function activate(array $tenant): array
    {
        $db = $tenant['db'] ?? [];
        if (empty($db['database'])) {
            throw new TenantResolutionException('NOT_PROVISIONED', 'The database for this account has not been configured yet.', 409);
        }

        $connection = config('database.default', 'mysql');

        // Baseline working database credentials from environment
        $defaultHost = config("database.connections.{$connection}.host") ?: env('DB_HOST', 'localhost');
        $defaultPort = (int) (config("database.connections.{$connection}.port") ?: env('DB_PORT', 3306));
        $defaultUsername = config("database.connections.{$connection}.username") ?: env('DB_USERNAME', 'root');
        $defaultPassword = (string) (config("database.connections.{$connection}.password") ?? env('DB_PASSWORD', ''));
        $defaultDatabase = config("database.connections.{$connection}.database") ?: env('DB_DATABASE', '');

        $dbHost = !empty($db['host']) ? $db['host'] : $defaultHost;
        $dbPort = (int) ($db['port'] ?? $defaultPort);

        $dbUsername = !empty($db['username']) ? $db['username'] : $defaultUsername;
        $dbPassword = (string) ($db['password'] ?? $defaultPassword);
        $databaseName = $db['database'];

        // If default environment credentials are non-root (e.g. Hostinger u573967329_xxx) and tenant specifies dummy 'root', use default credentials
        if ($defaultUsername !== 'root' && !empty($defaultUsername) && ($dbUsername === 'root' || empty($dbUsername))) {
            $dbUsername = $defaultUsername;
            $dbPassword = $defaultPassword;
            $dbHost = $defaultHost;
        }

        // Test connecting with PDO: try target host, and if Operation not permitted (2002), try alternate (localhost <-> 127.0.0.1)
        $pdo = null;
        if (!empty($dbUsername)) {
            $hostsToTry = array_unique([$dbHost, 'localhost', '127.0.0.1']);
            foreach ($hostsToTry as $tryHost) {
                try {
                    $dsn = ($tryHost === 'localhost') ? "mysql:host=localhost" : "mysql:host={$tryHost};port={$dbPort}";
                    $pdo = new \PDO($dsn, $dbUsername, $dbPassword, [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
                        \PDO::ATTR_TIMEOUT => 2,
                    ]);
                    if ($pdo) {
                        $dbHost = $tryHost;
                        break;
                    }
                } catch (\Throwable) {
                    $pdo = null;
                }
            }
        }

        // If tenant specific credentials cannot connect, fallback to environment default credentials
        if (!$pdo) {
            $hostsToTry = array_unique([$defaultHost, 'localhost', '127.0.0.1']);
            foreach ($hostsToTry as $tryHost) {
                try {
                    $dsn = ($tryHost === 'localhost') ? "mysql:host=localhost" : "mysql:host={$tryHost};port={$defaultPort}";
                    $pdo = new \PDO($dsn, $defaultUsername, $defaultPassword, [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
                        \PDO::ATTR_TIMEOUT => 2,
                    ]);
                    if ($pdo) {
                        $dbHost = $tryHost;
                        $dbPort = $defaultPort;
                        $dbUsername = $defaultUsername;
                        $dbPassword = $defaultPassword;
                        break;
                    }
                } catch (\Throwable) {
                    $pdo = null;
                }
            }
        }

        if ($pdo) {
            try {
                $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($databaseName));
                if (!$stmt || !$stmt->fetch()) {
                    $found = false;
                    // Check if prefixed with shared host username (e.g. u573967329_clinicdb)
                    if (!empty($defaultDatabase) && preg_match('/^(u\d+_)/', $defaultDatabase, $m)) {
                        $prefixedDb = $m[1] . $databaseName;
                        $chk = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($prefixedDb));
                        if ($chk && $chk->fetch()) {
                            $databaseName = $prefixedDb;
                            $found = true;
                        }
                    }

                    if (!$found) {
                        // Check if stripped prefix database exists (e.g. u573967329_clinicdb -> clinicdb)
                        $cleanDbName = preg_replace('/^u\d+_/', '', $databaseName);
                        $checkCleanStmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($cleanDbName));
                        if ($checkCleanStmt && $checkCleanStmt->fetch()) {
                            $databaseName = $cleanDbName;
                            $found = true;
                        }
                    }

                    if (!$found) {
                        if (app()->environment('local') && preg_match('/^[a-zA-Z0-9_]+$/', $databaseName)) {
                            // On local dev, auto-create database if not exists
                            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        } elseif (!empty($defaultDatabase)) {
                            // On shared hosting if dedicated tenant DB is not created, fallback to default DB from .env
                            $databaseName = $defaultDatabase;
                        }
                    }
                }
            } catch (\Throwable) {
                if (!empty($defaultDatabase)) {
                    $databaseName = $defaultDatabase;
                }
            }
        }

        config([
            "database.connections.{$connection}.host" => $dbHost,
            "database.connections.{$connection}.port" => $dbPort,
            "database.connections.{$connection}.database" => $databaseName,
            "database.connections.{$connection}.username" => $dbUsername,
            "database.connections.{$connection}.password" => $dbPassword,
        ]);
        DB::purge($connection);

        $this->isolateFilesystem((int) $tenant['id']);

        Log::shareContext(['tenant_id' => (int) $tenant['id']]);

        $this->tenant = $tenant;
        app()->instance('tenant', $tenant);

        // Keep tenant modules and permissions synced with active plan / assigned modules
        try {
            static $alreadySynced = false;
            if (!$alreadySynced) {
                $alreadySynced = true;
                \App\Services\PlanPermissionSyncService::syncPlanModulesAndPermissions();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PlanPermissionSyncService error during tenant activation: ' . $e->getMessage());
        }

        return $tenant;
    }

    public function forgetCachedHost(string $host): void
    {
        $host = static::normalizeHost($host);
        $key = 'host:' . sha1($host . '|' . config('tenant.product_code') . '|' . config('tenant.project_code'));
        $this->lookupCache()->forget($key);
        $this->lookupCache()->forget($key . ':stale');
    }

    /**
     * Uploads, generated PDFs, file cache and sessions live under
     * storage/tenants/{id}, so code calling storage_path() or the public
     * disk is isolated per client without modification.
     */
    private function isolateFilesystem(int $tenantId): void
    {
        $root = $this->sharedStoragePath('tenants' . DIRECTORY_SEPARATOR . $tenantId);

        foreach (['app/public', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
            File::ensureDirectoryExists($root . DIRECTORY_SEPARATOR . $dir, 0755);
        }

        app()->useStoragePath($root);

        $this->baseSessionCookie ??= (string) config('session.cookie');

        config([
            'filesystems.disks.local.root' => $root . DIRECTORY_SEPARATOR . 'app',
            'filesystems.disks.public.root' => $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public',
            'cache.stores.file.path' => $root . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'data',
            'cache.prefix' => 'tenant_' . $tenantId . '_',
            'session.files' => $root . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
            'session.cookie' => $this->baseSessionCookie . '_t' . $tenantId,
        ]);

        Storage::forgetDisk(['local', 'public']);
        Cache::forgetDriver('file');
        Cache::forgetDriver(config('cache.default'));
    }

    private function tenantFromResponse(Response $response, string $key): array
    {
        $body = (array) $response->json();

        if ($response->successful() && ! empty($body['success']) && ! empty($body['tenant'])) {
            if (! $this->belongsToThisApp($body['tenant'])) {
                throw new TenantResolutionException('PRODUCT_MISMATCH', 'This account does not belong to this product.', 403);
            }

            $tenant = $body['tenant'];
            if (! empty($tenant['id'])) {
                $idKey = 'tenant_id:' . (int) $tenant['id'] . '|' . config('tenant.product_code') . '|' . config('tenant.project_code');
                $this->lookupCache()->put($idKey, $tenant, max(1, (int) config('tenant.cache_seconds')));
                $this->lookupCache()->put($idKey . ':stale', $tenant, max(1, (int) config('tenant.grace_seconds')));
            }

            return $tenant;
        }

        // A definitive "no" from the admin panel must not be overridden by the grace copy.
        $this->lookupCache()->forget($key);
        $this->lookupCache()->forget($key . ':stale');

        throw new TenantResolutionException(
            (string) ($body['code'] ?? 'UNKNOWN'),
            (string) ($body['message'] ?? 'Unable to load your account.'),
            $response->status() >= 400 ? $response->status() : 403,
            $body
        );
    }

    private function belongsToThisApp(array $tenant): bool
    {
        $product = strtoupper((string) config('tenant.product_code'));
        $project = strtoupper((string) config('tenant.project_code'));

        $tProduct = strtoupper((string) ($tenant['product_code'] ?? ''));
        $tProject = strtoupper((string) ($tenant['project_code'] ?? ''));

        // Allow flexible aliases for HMS/Clinic products (e.g. HMS, CLINIC, HOSPITAL_HMS)
        $hmsProductAliases = ['HMS', 'CLINIC', 'HOSPITAL', 'HOSPITAL_HMS', 'CLINIC_ERP', 'HMS_ERP', 'FABLEAD_HMS', 'FABLEAD_CLINIC'];
        $isThisHmsApp = in_array($product, $hmsProductAliases, true) || in_array($project, $hmsProductAliases, true);

        if ($isThisHmsApp) {
            $isHmsTenant = in_array($tProduct, $hmsProductAliases, true)
                || in_array($tProject, $hmsProductAliases, true)
                || str_contains(strtolower($tenant['domain'] ?? ''), 'hms')
                || str_contains(strtolower($tenant['domain'] ?? ''), 'clinic')
                || str_contains(strtolower($tenant['db']['database'] ?? ''), 'hms')
                || str_contains(strtolower($tenant['db']['database'] ?? ''), 'clinic');

            if ($isHmsTenant) {
                return true;
            }

            // In console commands targeting a specific tenant ID, allow it
            if (app()->runningInConsole() && !empty($tenant['id']) && (int) $tenant['id'] === (int) getenv('TENANT_ID')) {
                return true;
            }
        }

        $productMatches = ($tProduct === '' || $product === '' || $tProduct === $product || ($isThisHmsApp && in_array($tProduct, $hmsProductAliases, true)));
        $projectMatches = ($tProject === '' || $project === '' || $tProject === $project || ($isThisHmsApp && in_array($tProject, $hmsProductAliases, true)));

        return $productMatches && $projectMatches;
    }

    private function activateStaleOrFail(string $key, string $reason): array
    {
        $stale = $this->lookupCache()->get($key . ':stale');

        Log::warning('Tenant API unavailable', ['reason' => $reason, 'using_stale' => is_array($stale)]);

        if (is_array($stale)) {
            return $this->activate($stale);
        }

        throw new TenantResolutionException('UNREACHABLE', 'Unable to reach the account server. Please try again in a few minutes.', 503);
    }

    private function client(): PendingRequest
    {
        $url = (string) config('tenant.api_url');
        if ($url === '' || strlen((string) config('tenant.secret')) < 32 || config('tenant.product_code') === '') {
            throw new TenantResolutionException(
                'NOT_CONFIGURED',
                'Tenancy is enabled but TENANT_API_URL, TENANT_API_SECRET (min 32 chars) or TENANT_PRODUCT_CODE is missing.',
                500
            );
        }

        return Http::baseUrl($url)
            ->acceptJson()
            ->asForm()
            ->timeout(max(1, (int) config('tenant.http_timeout')))
            ->withHeaders(['X-Tenant-Secret' => (string) config('tenant.secret')]);
    }

    private function lookupCache(): Repository
    {
        if ($this->lookupCache === null) {
            $path = $this->sharedStoragePath('framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'tenant-lookup');
            File::ensureDirectoryExists($path, 0755);
            $this->lookupCache = Cache::repository(new FileStore(app('files'), $path));
        }

        return $this->lookupCache;
    }
}

