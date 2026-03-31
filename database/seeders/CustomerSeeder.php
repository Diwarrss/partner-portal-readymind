<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('code', 'MX')->first();
        if (! $tenant) {
            return;
        }

        $customers = [
            [
                'email' => 'cliente@readymind.ms',
                'name' => 'Cliente Demo',
            ],
            [
                'email' => 'dialvaro@readymind.ms',
                'name' => 'Dialvaro',
            ],
        ];

        foreach ($customers as $row) {
            $customer = Customer::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'email' => $row['email'],
                ],
                [
                    'name' => $row['name'],
                    'password' => bcrypt('password'),
                    'is_active' => true,
                ]
            );

            if (! $customer->external_id) {
                $customer->update(['external_id' => (string) Str::uuid()]);
            }
        }
    }
}
