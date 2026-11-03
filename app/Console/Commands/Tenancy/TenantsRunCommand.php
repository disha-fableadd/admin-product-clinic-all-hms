<?php

namespace App\Console\Commands\Tenancy;

use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;

class TenantsRunCommand extends Command
{
    use RunsForTenants;

    protected $signature = 'tenants:run
                            {artisanCommand : The artisan command to run, in quotes, e.g. "schedule:run"}
                            {--tenant= : Only this tenant (setup ID in admin_erp)}
                            {--all : Include inactive / expired tenants}';

    protected $description = 'Run an artisan command for every active client';

    public function handle(TenantManager $tenancy): int
    {
        $arguments = str_getcsv(trim((string) $this->argument('artisanCommand')), ' ');
        $arguments = array_values(array_filter($arguments, fn ($a) => $a !== ''));

        if ($arguments === [] || str_starts_with($arguments[0], 'tenants:')) {
            $this->error('Provide a normal artisan command, e.g. tenants:run "schedule:run".');

            return self::FAILURE;
        }

        $tenants = $this->tenantsOrFail($tenancy, ! $this->option('all'), (int) $this->option('tenant'));
        if ($tenants === null) {
            return self::FAILURE;
        }

        $failed = 0;
        foreach ($tenants as $tenant) {
            $failed += $this->runArtisanForTenant($tenant, $arguments) ? 0 : 1;
        }

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
