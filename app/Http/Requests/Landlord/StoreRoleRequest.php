<?php

namespace App\Http\Requests\Landlord;

use App\Models\Landlord\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
        ];

        if (!tenant()) {
            $rules['tenant_id'] = ['nullable', 'integer', 'exists:tenants,id'];
            $rules['is_tenant_base'] = ['required', 'boolean'];
        } else {
            $rules['tenant_id'] = ['missing'];
            $rules['is_tenant_base'] = ['missing'];
        }

        return $rules;
    }
}
