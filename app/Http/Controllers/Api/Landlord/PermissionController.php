<?php

namespace App\Http\Controllers\Api\Landlord;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\StorePermissionRequest;
use App\Http\Requests\Landlord\UpdatePermissionRequest;
use App\Models\Landlord\Permission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        $query = Permission::query()->verifiedTenantUser();

        $permissions = $this->paginateIndex($request, $query);

        return ApiResponse::successResponse(
            'Permission list retrieved successfully.',
            $permissions['data'],
            $permissions['pagination_meta'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::firstOrCreate(
            ['name' => $request->input('name')],
            $request->validated()
        );

        return ApiResponse::successResponse(
            'Permission created successfully.',
            $permission,
            null,
            ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        return ApiResponse::successResponse(
            'Permission retrieved successfully.',
            $permission,
            null,
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return ApiResponse::successResponse(
            'Permission updated successfully.',
            $permission,
            null,
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Permission $permission)
    {
        if ($request->has('force') && $request->get('force') === 'true') {
            $this->authorize('forceDelete', $permission);
            $permission->forceDelete();
        } else {
            $this->authorize('delete', $permission);
            $permission->delete();
        }

        return ApiResponse::successResponse(
            'Permission deleted successfully.',
            null,
            null,
            ResponseAlias::HTTP_NO_CONTENT
        );
    }
}
