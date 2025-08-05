<?php

namespace App\Models\Base;

use App\Traits\HasRegisters;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    use HasRegisters;

    protected static function boot(): void
    {
        parent::boot();
        static::bootTraits();
    }
}
