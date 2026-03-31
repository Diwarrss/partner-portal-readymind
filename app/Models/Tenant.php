<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'code',
        'name',
        'partner_center_client_id',
        'partner_center_client_secret',
        'partner_center_tenant_id',
        'is_sandbox',
    ];

    protected function casts(): array
    {
        return [
            'is_sandbox' => 'boolean',
            'partner_center_client_secret' => 'encrypted',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
