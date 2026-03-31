<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'external_id',
        'tenant_id',
        'email',
        'password',
        'name',
        'microsoft_tenant_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function licenseSubscriptions(): HasMany
    {
        return $this->hasMany(LicenseSubscription::class);
    }

    public function licensePrices(): HasMany
    {
        return $this->hasMany(LicensePrice::class);
    }

    public function productRequests(): HasMany
    {
        return $this->hasMany(ProductRequest::class);
    }
}
