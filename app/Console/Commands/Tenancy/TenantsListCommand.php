<?php

namespace App\Console\Commands\Tenancy;

use App\Exceptions\TenantResolutionException;
use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;

class TenantsListCommand extends Command
{
    protected $signature = 'tenants:list';

    protected $description = 'List every client (tenant) of this product registered in admin_erp';

    public function handle(TenantManager $tenancy): int
    {
        if (! $tenancy->enabled()) {
            $this->error('Tenancy is disabled. Set TENANCY_ENABLED=true in .env first.');

            return self::FAILURE;
        }

        try {
            $tenants = $tenancy->all();
        } catch (TenantResolutionException $e) {
            $this->error("[{$e->reason}] {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->table(
            ['ID', 'Client', 'Domain', 'Database', 'Host', 'Status', 'Membership', 'Expiry', 'Usable'],
            array_map(fn ($t) => [
                $t['id'],
                $t['client_name'] ?? '',
                $t['domain'] ?? '',
                $t['db']['database'] ?? '',
                ($t['db']['host'] ?? '') . ':' . ($t['db']['port'] ?? ''),
                $t['status'] ?? '',
                $t['membership_status'] ?? '',
                $t['expiry_date'] ?? '',
                $tenancy->isUsable($t) ? 'yes' : 'no',
            ], $tenants)
        );

        return self::SUCCESS;
    }
}
