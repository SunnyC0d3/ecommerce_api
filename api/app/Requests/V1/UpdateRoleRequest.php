<?php

namespace App\Requests\V1;

class UpdateRoleRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id ?? 'null';

        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
        ];
    }
}
