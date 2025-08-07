<?php

namespace App\Models\Tenant;

use App\Models\Base\TenantModel;
use App\Traits\HasDefaultStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends TenantModel
{
    use SoftDeletes, HasDefaultStatus;

    protected $fillable = [
        'name',
        'description',
        'status',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];
}
