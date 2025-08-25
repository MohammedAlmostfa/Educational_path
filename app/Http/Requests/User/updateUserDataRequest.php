<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class UpdateUserDataRequest
 *
 * Handles validation for updating user profile data.
 * Fields are optional (nullable) since this is an update request.
 */
class UpdateUserDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Allow all authenticated users to make this request
        return true;
    }

    /**
     * Validation rules for updating user data.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            // Average is optional, numeric, and between 0 and 100
            'average'   => 'nullable|numeric|min:0|max:100',

            // Gender is optional and must be 0 (male) or 1 (female)
            'gender'    => 'nullable|in:0,1',

            // Branch ID is optional but must exist in the branches table
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'average.required'   => 'حقل المعدل مطلوب.',
            'average.numeric'    => 'المعدل يجب أن يكون رقمًا.',
            'average.min'        => 'المعدل يجب أن يكون على الأقل 0.',
            'average.max'        => 'المعدل يجب أن لا يزيد عن 100.',

            'gender.required'    => 'حقل الجنس مطلوب.',
            'gender.in'          => 'الجنس يجب أن يكون للذكر أو للانثى.',

            'branch_id.required' => 'حقل الفرع مطلوب.',
            'branch_id.exists'   => 'الفرع المختار غير موجود.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        // Return a JSON response if validation fails
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'فشل التحقق من صحة البيانات',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
