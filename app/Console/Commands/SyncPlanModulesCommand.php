<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Services\PlanPermissionSyncService;
use Illuminate\Console\Command;

class SyncPlanModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:sync-modules {--plan= : ID of the plan to sync (defaults to active plan)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize tenant modules and user_permissions tables according to the active plan features';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $planId = $this->option('plan');
        $plan = null;

        if ($planId) {
            $plan = Plan::find($planId);
            if (!$plan) {
                $this->error("Plan with ID {$planId} not found.");
                return self::FAILURE;
            }
        } else {
            $plan = Plan::where('is_active', 1)->latest('id')->first()
                ?: Plan::latest('id')->first();
        }

        if (!$plan) {
            $this->warn('No plan found in the database. Syncing standard modules by default...');
        } else {
            $this->info("Syncing modules for plan: {$plan->name} (ID: {$plan->id})...");
        }

        $result = PlanPermissionSyncService::syncPlanModulesAndPermissions($plan);

        $this->info("Successfully synced {$result['synced_modules_count']} modules into the 'modules' and 'user_permissions' tables.");
        $this->line("Allowed Module IDs: " . implode(', ', $result['allowed_module_ids']));

        return self::SUCCESS;
    }
}

