<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::updateOrCreate(
            ['code' => 'MX'],
            ['name' => 'México', 'is_sandbox' => true]
        );
        Tenant::updateOrCreate(
            ['code' => 'USA'],
            ['name' => 'United States', 'is_sandbox' => true]
        );
        Tenant::updateOrCreate(
            ['code' => 'PR'],
            ['name' => 'Puerto Rico', 'is_sandbox' => true]
        );
    }
}
