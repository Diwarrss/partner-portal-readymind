<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseSubscription extends Model
{
    protected $fillable = [
        'customer_id',
        'partner_subscription_id',
        'offer_id',
        'offer_name',
        'quantity',
        'status',
        'cancellation_allowed_until_date',
        'creation_date',
        'refundable_quantity',
        'etag',
        'raw_data',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'synced_at' => 'datetime',
            'cancellation_allowed_until_date' => 'datetime',
            'creation_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
