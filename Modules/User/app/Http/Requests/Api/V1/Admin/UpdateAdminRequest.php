<?php
namespace Modules\User\Http\Requests\Api\V1\Admin;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adminId = $this->route('id');

        return [
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:admins,email,' . $adminId,
            'password' => 'sometimes|string|min:6',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422));
    }
}
