<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class checkActivationCodeRequest
 *
 * Handles validation for user activation code submission.
 * Ensures the activation code is present and is an integer.
 */
class checkActivationCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Allow all users to execute this request
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Validation rules: activation code is required and must be an integer
        return [
            'activation_code' => 'required|integer'
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        // Arabic messages for validation errors
        return [
            'activation_code.required' => 'كود التفعيل مطلوب.',
            'activation_code.integer'  => 'كود التفعيل يجب أن تكون رقماً.',
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
        // Return a JSON response on validation failure
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'فشل التحقق من صحة البيانات',
            'errors' => $validator->errors(),
        ], 422));
    }
}
