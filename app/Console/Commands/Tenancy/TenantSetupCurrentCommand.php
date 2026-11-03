<?php

namespace App\Console\Commands\Tenancy;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\ClientRepository;

class TenantSetupCurrentCommand extends Command
{
    protected $signature = 'tenants:setup-current {--admin-email=} {--admin-username=} {--admin-password=} {--admin-name=} {--admin-phone=}';

    protected $description = 'Internal: set up the tenant selected by TENANT_ID (use tenants:provision)';

    protected $hidden = true;

    public function handle(ClientRepository $clients): int
    {
        if (! tenant()) {
            $this->error('No tenant is active. Run this through: php artisan tenants:provision {id}');

            return self::FAILURE;
        }

        $this->info('Database: ' . tenant('db.database'));

        if (Artisan::call('migrate', ['--force' => true], $this->output) !== 0) {
            return self::FAILURE;
        }

        // The default seeder sets default admin user, only seed brand-new databases.
        if (User::query()->count() === 0) {
            if (Artisan::call('db:seed', ['--force' => true], $this->output) !== 0) {
                return self::FAILURE;
            }
        } else {
            $this->line('Users already exist — initial db:seed skipped.');
        }

        // Delete demo dummy users so only the client admin exists
        User::whereIn('email', [
            'subadmin@gmail.com',
            'staff@gmail.com',
            'customer@gmail.com',
            'vendor@gmail.com',
        ])->delete();

        $email = $this->option('admin-email') ?: tenant('email');
        $username = $this->option('admin-username');
        $password = $this->option('admin-password');
        $name = $this->option('admin-name') ?: tenant('client_name');
        $phone = $this->option('admin-phone') ?: tenant('mobile') ?: tenant('phone');

        $admin = User::where('email', 'admin@gmail.com')->first();
        if (! $admin && $email) {
            $admin = User::where('email', $email)->first();
        }
        if (! $admin) {
            $admin = User::where('role', 'admin')->first() ?: User::orderBy('id')->first();
        }
        if ($admin) {
            $fill = [];
            if ($email) {
                $fill['email'] = $email;
            }
            if ($name && Schema::hasColumn('users', 'name')) {
                $fill['name'] = $name;
            }
            if ($phone && Schema::hasColumn('users', 'phone')) {
                $fill['phone'] = $phone;
            }
            if ($password) {
                $fill['password'] = Hash::make($password);
            }
            if ($username && Schema::hasColumn('users', 'username')) {
                $fill['username'] = strtolower(trim($username));
            }
            if (!empty($fill)) {
                $admin->forceFill($fill)->save();
            }
            $this->info('Admin login synced: ' . ($email ?: $admin->email) . ' (phone: ' . ($phone ?: $admin->phone ?: 'none') . ')');
        }

        if (Schema::hasTable('oauth_personal_access_clients') && ! DB::table('oauth_personal_access_clients')->exists()) {
            $appName = config('app.name', 'Hotel ERP');
            $clients->createPersonalAccessClient(null, $appName . ' Personal Access Client', 'http://localhost');
            $clients->createPasswordGrantClient(null, $appName . ' Password Grant Client', 'http://localhost', 'users');
            $this->info('Passport clients created.');
        } else {
            $this->line('Passport clients already present.');
        }

        $this->info('Tenant #' . tenant('id') . ' is ready.');

        return self::SUCCESS;
    }
}
