<?php

namespace App\Http\Requests\Landlord;

use App\Models\Landlord\Permission;
use App\Models\Landlord\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Role::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_tenant_base' => ['nullable', 'boolean', 'default:false'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'array', 'distinct'],
            'permissions.*.action' => ['required', 'string', Rule::in(Permission::BASE_PERMISSIONS)],
        ];

        if (tenant()) {
            $rules['permissions.*.resource'] = ['required', 'string', Rule::in(Permission::TENANT_RESOURCES)];
        }
        else {
            $rules['permissions.*.resource'] = ['required', 'string', Rule::in(Permission::RESOURCES)];
        }

        return $rules;
    }
}
