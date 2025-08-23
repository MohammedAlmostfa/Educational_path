<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
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

    
public function messages()
{
    return [
        'email.required' => 'البريد الإلكتروني مطلوب.',
        'email.email' => 'يجب إدخال بريد إلكتروني صالح.',
        'password.required' => 'كلمة المرور مطلوبة.',
        'password.string' => 'كلمة المرور يجب أن تكون نصاً.',

    ];
}
}
