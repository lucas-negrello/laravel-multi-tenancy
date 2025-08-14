<?php

namespace App\Services\Utils\Auth;

use App\Models\Landlord\User;
use App\Services\Utils\Landlord\UserService;
use Illuminate\Http\Request;

class MeService {
    public function getMeInfo(Request $request): array
    {
        return UserService::loggedUserToArray($request);
    }
}
