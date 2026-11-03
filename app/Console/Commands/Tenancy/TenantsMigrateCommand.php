<?php

namespace App\Console\Commands\Tenancy;

use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;

class TenantsMigrateCommand extends Command
{
    use RunsForTenants;

    protected $signature = 'tenants:migrate
                            {--tenant= : Only this tenant (setup ID in admin_erp)}
                            {--seed : Run database seeders after migrating}
                            {--pretend : Show the SQL without running it}';

    protected $description = 'Run database migrations on every client database';

    public function handle(TenantManager $tenancy): int
    {
        $tenants = $this->tenantsOrFail($tenancy, false, (int) $this->option('tenant'));
        if ($tenants === null) {
            return self::FAILURE;
        }

        $arguments = ['migrate', '--force'];
        if ($this->option('seed')) {
            $arguments[] = '--seed';
        }
        if ($this->option('pretend')) {
            $arguments[] = '--pretend';
        }

        $failed = 0;
        foreach ($tenants as $tenant) {
            $failed += $this->runArtisanForTenant($tenant, $arguments) ? 0 : 1;
        }

        $this->newLine();
        $this->info(sprintf('Done: %d succeeded, %d failed.', count($tenants) - $failed, $failed));

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
