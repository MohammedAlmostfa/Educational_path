<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class RegisterRequest
 *
 * Handles validation for user registration requests.
 * Ensures email and password are provided and meet the required criteria.
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all requests
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|email|unique:users,email', // Must be unique and valid
            'password' => 'required|string|min:8',             // Must be at least 8 characters
        ];
    }

    /**
     * Custom messages for validation failures (in Arabic).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email'    => 'يجب إدخال بريد إلكتروني صالح.',
            'email.unique'   => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string'   => 'كلمة المرور يجب أن تكون نصاً.',
            'password.min'      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     * Returns a JSON response with errors.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'فشل التحقق من صحة البيانات',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
