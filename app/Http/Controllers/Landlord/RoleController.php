<?php

namespace App\Http\Controllers\Landlord;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StoreRoleRequest;
use App\Http\Requests\Landlord\UpdateRoleRequest;
use App\Models\Landlord\Permission;
use App\Models\Landlord\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $query = Role::query()->verifiedTenantUser();

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
    public function store(StoreRoleRequest $request)
    {
        $role = Role::firstOrCreate(
            ['name' => $request->input('name')],
            $request->validated()
        );

        $permissions = $request->input('permissions', []);
        if (!empty($permissions)) {
            $permissionIds = [];
            foreach ($permissions as $permission) {
                $p = Permission::firstOrCreate(
                    ['name' => $permission['resource'].'_'.$permission['action']],
                    ['description' => '']
                );
                $permissionIds[] = $p->id;
            }
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }

        $role = $role->refresh();

        return ApiResponse::successResponse(
            'Role created successfully.',
            $role,
            null,
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
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
    public function update(UpdateRoleRequest $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
