<?php

namespace App\Http\Controllers\Api\Landlord;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreUserRequest;
use App\Http\Requests\Landlord\UpdateUserRequest;
use App\Models\Landlord\User;
use App\Services\Utils\Landlord\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->verifiedTenantUser();

        $users = $this->paginateIndex($request, $query);

        $formattedUsers = UserService::usersToArray($users['data']);

        return ApiResponse::successResponse(
            'User list retrieved successfully.',
            $formattedUsers,
            $users['pagination_meta'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            ...$request->validated(),
            'email_verified_at' => Carbon::now(),
        ]);

        if ($tenant = tenant()) {
            $user->tenants()->syncWithoutDetaching([$tenant->getKey()]);
        }

        UserService::attachRoleFromStoreRequest($request, $user);

        return ApiResponse::successResponse(
            'User created successfully.',
            $user,
            null,
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $formattedUser = UserService::userToArray($user);

        return ApiResponse::successResponse(
            'User retrieved successfully.',
            $formattedUser,
            null,
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return ApiResponse::successResponse(
            'User updated successfully.',
            $user,
            null,
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->has('force') && $request->get('force') === 'true') {
            $this->authorize('forceDelete', $user);
            $user->forceDelete();
        } else {
            $this->authorize('delete', $user);
            $tenant = tenant();
            if ($tenant)
                $user->detachTenant($tenant->getKey());
            if ($user->tenants()->count() === 0)
                $user->delete();
        }

        return ApiResponse::successResponse(
            'User deleted successfully.',
            null,
            null,
            ResponseAlias::HTTP_NO_CONTENT
        );
    }
}
