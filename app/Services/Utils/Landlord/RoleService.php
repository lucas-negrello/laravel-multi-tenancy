<?php

namespace App\Services\Utils\Landlord;

use App\Http\Requests\Landlord\StoreRoleRequest;
use App\Http\Requests\Landlord\UpdateRoleRequest;
use App\Models\Landlord\Role;
use Exception;
use Illuminate\Http\Request;

class RoleService
{
    /**
     * @param StoreRoleRequest $request
     * @return Role
     */
    public static function create(StoreRoleRequest $request): Role
    {
        if (!tenant()) {
            $role = Role::firstOrCreate(
                [
                    'name' => $request->input('name'),
                    'tenant_id' => $request->input('tenant_id'),
                    'is_tenant_base' => $request->input('is_tenant_base'),
                ],
                $request->validated()
            );
        } else {
            $role = Role::firstOrCreate(
                [
                    'name' => $request->input('name'),
                    'tenant_id' => tenant()->getKey(),
                    'is_tenant_base' => true,
                ],
                [
                    ...$request->validated(),
                    'tenant_id' => tenant()->getKey(),
                    'is_tenant_base' => true,
                ]
            );
        }

        return $role;
    }

    /**
     * @param Role $role
     * @param UpdateRoleRequest $request
     * @return Role
     */
    public static function update(Role $role, UpdateRoleRequest $request): Role
    {
        if (!tenant()) {
            $role->update($request->validated());
        } else {
            $role->update([
                ...$request->validated(),
                'tenant_id' => tenant()->getKey(),
                'is_tenant_base' => true,
            ]);
        }

        return $role;
    }

    /**
     * @param Request $request
     * @return 'delete'|'forceDelete'
     */
    public static function deleteAbility(Request $request): string
    {
        if ($request->has('force') && $request->get('force') === 'true') {
            return 'forceDelete';
        }
        return 'delete';
    }

    /**
     * @param Request $request
     * @param Role $role
     * @return bool|null
     * @throws Exception
     */
    public static function delete(Request $request, Role $role): ?bool
    {
        $ability = RoleService::deleteAbility($request);
        if ($ability === 'forceDelete') return $role->forceDelete();
        if ($ability === 'delete') return $role->delete();
        throw new Exception('Invalid delete ability');
    }
}
