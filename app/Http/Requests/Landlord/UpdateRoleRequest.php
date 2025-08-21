<?php

namespace App\Http\Requests\Landlord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $role = $this->route('role');
        if (tenant() && !$role->is_tenant_base) return false;
        return Gate::allows('update', $role);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        if (!tenant()) {
            $rules['tenant_id'] = ['nullable', 'integer', 'exists:tenants,id'];
            $rules['is_tenant_base'] = ['nullable', 'boolean'];
        } else {
            $rules['tenant_id'] = ['missing'];
            $rules['is_tenant_base'] = ['missing'];
        }

        return $rules;
    }
}
