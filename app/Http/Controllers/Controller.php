<?php

namespace App\Http\Controllers;

use App\Traits\HasPaginatedResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, HasPaginatedResponse;
}
