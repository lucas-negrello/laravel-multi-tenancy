<?php

namespace App\Models\Base;


use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class LandlordModel extends BaseModel
{
    use UsesLandlordConnection;
}
