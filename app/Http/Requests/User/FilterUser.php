<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class FilterUser
 *
 * Handles validation for filtering users.
 * Currently allows filtering by optional email.
 */
class FilterUser extends FormRequest
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
            // Email is optional for filtering
            'name'=>'nullable',
            'email' => 'nullable',
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
                'status' => 'error',
                'message' => 'فشل التحقق من صحة البيانات',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
