<?php

namespace App\Console\Commands\Tenancy;

use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;

class TenantsProvisionCommand extends Command
{
    use RunsForTenants;

    protected $signature = 'tenants:provision
                            {tenant : Setup ID of the client in admin_erp}
                            {--admin-email= : Login email for the first admin (new databases only)}
                            {--admin-username= : Login username for the first admin (new databases only)}
                            {--admin-password= : Password for the first admin (new databases only)}
                            {--admin-name= : Full name for the first admin}
                            {--admin-phone= : Phone number for the first admin}';

    protected $description = 'Prepare a client database: migrate, seed if empty, create Passport clients';

    public function handle(TenantManager $tenancy): int
    {
        $tenants = $this->tenantsOrFail($tenancy, false, (int) $this->argument('tenant'));
        if (! $tenants) {
            return self::FAILURE;
        }

        $arguments = ['tenants:setup-current'];
        if ($email = $this->option('admin-email')) {
            $arguments[] = '--admin-email=' . $email;
        }
        if ($username = $this->option('admin-username')) {
            $arguments[] = '--admin-username=' . $username;
        }
        if ($password = $this->option('admin-password')) {
            $arguments[] = '--admin-password=' . $password;
        }
        if ($name = $this->option('admin-name')) {
            $arguments[] = '--admin-name=' . $name;
        }
        if ($phone = $this->option('admin-phone')) {
            $arguments[] = '--admin-phone=' . $phone;
        }

        return $this->runArtisanForTenant($tenants[0], $arguments) ? self::SUCCESS : self::FAILURE;
    }
}
