<?php

namespace App\Console\Commands\Tenancy;

use App\Exceptions\TenantResolutionException;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Facades\Process;

/**
 * Each tenant runs in its own `php artisan` child process with TENANT_ID set,
 * so state never leaks between clients and anything that command spawns
 * inherits the same tenant.
 */
trait RunsForTenants
{
    /**
     * @return array<int, array>|null
     */
    protected function tenantsOrFail(TenantManager $tenancy, bool $onlyUsable, ?int $tenantId = null): ?array
    {
        if (! $tenancy->enabled()) {
            $this->error('Tenancy is disabled. Set TENANCY_ENABLED=true in .env first.');

            return null;
        }

        try {
            $tenants = $tenancy->all($tenantId ?: null);
        } catch (TenantResolutionException $e) {
            $this->error("[{$e->reason}] {$e->getMessage()}");

            return null;
        }

        if ($onlyUsable) {
            $tenants = array_values(array_filter($tenants, fn ($t) => $tenancy->isUsable($t)));
        }

        if ($tenants === []) {
            $this->warn('No matching tenants found.');
        }

        return $tenants;
    }

    /**
     * @param array<int, string> $arguments artisan command and its arguments
     */
    protected function runArtisanForTenant(array $tenant, array $arguments): bool
    {
        $label = sprintf('#%d %s (%s)', $tenant['id'], $tenant['client_name'] ?? '', $tenant['db']['database'] ?? '?');
        $this->line("<fg=cyan>▶ {$label}</>: php artisan " . implode(' ', $arguments));

        $db = $tenant['db'] ?? [];
        $env = [
            'TENANT_ID' => (string) $tenant['id'],
        ];
        if (!empty($db['database'])) {
            $env['DB_DATABASE'] = $db['database'];
            $env['DB_HOST'] = $db['host'] ?? '127.0.0.1';
            $env['DB_PORT'] = (string) ($db['port'] ?? 3306);
            $env['DB_USERNAME'] = $db['username'] ?? 'root';
            $env['DB_PASSWORD'] = (string) ($db['password'] ?? '');
        }

        $result = Process::path(base_path())
            ->env($env)
            ->forever()
            ->run(array_merge([PHP_BINARY, 'artisan'], $arguments), function (string $type, string $output) {
                $this->output->write($output);
            });

        if (! $result->successful()) {
            $this->error("  ✗ failed for {$label} (exit {$result->exitCode()})");

            return false;
        }

        return true;
    }
}
