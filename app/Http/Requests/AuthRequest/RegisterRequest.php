<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
{
    return [
        'email.required' => 'البريد الإلكتروني مطلوب.',
        'email.email' => 'يجب إدخال بريد إلكتروني صالح.',
        'password.required' => 'كلمة المرور مطلوبة.',
        'password.string' => 'كلمة المرور يجب أن تكون نصاً.',
        'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
    ];
}
    /**
       * Handle a failed validation attempt.
       * This method is called when validation fails.
       * Logs failed attempts and throws validation exception.
       * @param \Illuminate\Validation\Validator $validator
       * @return void
       *
       */

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'فشل التحقق من صحة البيانات',
            'errors' => $validator->errors(),
        ], 422));
    }
}
