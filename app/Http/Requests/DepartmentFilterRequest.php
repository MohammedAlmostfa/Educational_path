<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class DepartmentFilterRequest
 *
 * Handles validation for filtering departments.
 */
class DepartmentFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // السماح لجميع المستخدمين بإرسال الطلب
    }

    /**
     * Validation rules.
     *
     * For filtering, 'name' should be an array of strings (optional).
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',

        ];
    }

    /**
     * Customize failed validation response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed for department filter data',
            'errors' => $validator->errors(),
        ], 422));
    }
}
