<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class CreateUserDataRequest
 *
 * Handles validation for creating or updating user profile data.
 * Ensures 'average', 'gender', and 'branch_id' are provided and valid.
 */
class CreateUserDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Allow all authenticated users to execute this request
        return true;
    }

    /**
     * Validation rules for the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'average'   => 'required|numeric|min:0|max:100',
            'gender'    => 'required|in:0,1', // Only 0 or 1 allowed
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    /**
     * Custom validation messages (in Arabic)
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
            'gender.in'          => 'الجنس يجب أن يكون  للذكر أو انتى.',

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
