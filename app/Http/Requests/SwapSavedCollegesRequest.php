<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class SwapSavedCollegesRequest
 *
 *  This FormRequest is responsible for validating the request
 * to swap two saved colleges.
 *
 * - Checks if the user is authorized (authorize).
 * - Applies validation rules on the request fields.
 * - Returns a custom JSON response when validation fails.
 */
class SwapSavedCollegesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *  Here, any authenticated user is allowed to perform this request.
     */
    public function authorize(): bool
    {
        // Allow any authenticated user to send this request
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     *  Rules:
     * - college_id_1: required, must be an integer, must exist in the colleges table.
     * - college_id_2: required, must be an integer, must exist in the colleges table.
     */
    public function rules(): array
    {
        return [
            'college_id_1' => 'required|integer|exists:colleges,id',
            'college_id_2' => 'required|integer|exists:colleges,id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @throws HttpResponseException
     *  When validation fails, return a custom JSON response
     * with an error message and validation details.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
