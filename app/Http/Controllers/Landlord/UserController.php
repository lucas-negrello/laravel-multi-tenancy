<?php

namespace App\Http\Controllers\Landlord;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreUserRequest;
use App\Http\Requests\Landlord\UpdateUserRequest;
use App\Models\Landlord\Role;
use App\Models\Landlord\User;
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

        $query = User::with([
            'roles',
            'permissions',
            'tenants'
        ]);

        $users = $this->paginateIndex($request, $query);

        return ApiResponse::successResponse(
            'User list retrieved successfully.',
            $users['data'],
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

        if ($request->input('role'))
            $user->assignRole($request->input('role'));
        else
            $user->assignRole(Role::USER);

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

        $user->load([
            'roles',
            'permissions',
            'tenants'
        ]);

        return ApiResponse::successResponse(
            'User retrieved successfully.',
            $user,
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
        if (!$request->has('force') && $request->get('force') === 'true') {
            $this->authorize('delete', $user);
            $user->delete();
        }
        else {
            $this->authorize('forceDelete', $user);
            $user->forceDelete();
        }

        return ApiResponse::successResponse(
            'User deleted successfully.',
            null,
            null,
            ResponseAlias::HTTP_NO_CONTENT
        );
    }
}
