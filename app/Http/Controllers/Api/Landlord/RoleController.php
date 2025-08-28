<?php

namespace App\Http\Controllers\Api\Landlord;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreRoleRequest;
use App\Http\Requests\Landlord\UpdateRoleRequest;
use App\Models\Landlord\Role;
use App\Services\Utils\Landlord\RoleService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $query = Role::query();

        if (tenant())
            $query->isTenantBase();

        $roles = $this->paginateIndex($request, $query);

        return ApiResponse::successResponse(
            'Role list retrieved successfully.',
            $roles['data'],
            $roles['pagination_meta'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = RoleService::create($request);

        return ApiResponse::successResponse(
            'Role created successfully.',
            $role,
            null,
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Role $role): JsonResponse
    {
        $this->authorize('view', $role);

        return ApiResponse::successResponse(
            'Role retrieved successfully.',
            $role,
            null,
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = RoleService::update($role, $request);

        return ApiResponse::successResponse(
            'Role updated successfully.',
            $role,
            null,
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     * @throws Exception
     */
    public function destroy(Request $request, Role $role): JsonResponse
    {
        $ability = RoleService::deleteAbility($request);
        $this->authorize($ability, $role);

        RoleService::delete($request, $role);

        $role->forceDelete();
        $role->delete();

        return ApiResponse::successResponse(
            'Role deleted successfully.',
            null,
            null,
            ResponseAlias::HTTP_NO_CONTENT
        );
    }
}
