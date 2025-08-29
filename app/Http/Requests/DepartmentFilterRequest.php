<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class DepartmentFilterRequest
 *
 * 📌 This FormRequest handles validation for filtering departments.
 *
 * - Authorization: Allows all users to send this request.
 * - Validation Rules: Optional string parameter for filtering by name.
 * - Custom Response: Returns a JSON response with an Arabic error message if validation fails.
 */
class DepartmentFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *  Allow all users to send this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for this request.
     *
     * @return array<string, string>
     *  Rules:
     * - name: optional string (nullable).
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @throws HttpResponseException
     *  Returns a JSON response with Arabic error message
     * if validation does not pass.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'فشل التحقق من صحة بيانات الفلترة الخاصة بالأقسام', // Arabic message
            'errors'  => $validator->errors(),
        ], 422));
    }
}
