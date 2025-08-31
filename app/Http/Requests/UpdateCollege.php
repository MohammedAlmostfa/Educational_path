<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class UpdateCollege
 *
 * A FormRequest to validate data when updating a college.
 *
 * Validates:
 * - Core college fields: name, university, type, study duration, gender, branch.
 * - Related fields: departments (Many-to-Many relationship).
 *
 * If validation fails, returns a JSON response with detailed errors.
 */
class UpdateCollege extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     *
     * Allows all users to perform this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     *
     * Validation rules:
     * - name: optional string, max 255 characters.
     * - university_id: optional, must exist in universities table.
     * - college_type: optional string, max 100 characters.
     * - study_duration: optional integer, minimum 1.
     * - gender: optional, must be one of 0=female, 1=male, 2=both.
     * - branch_id: optional, must exist in branches table.
     * - departments: optional array of IDs existing in the departments table.
     */
    public function rules(): array
    {

{
    return [
        'name' => 'nullable|string|max:255',
        'university_id' => 'nullable|exists:universities,id',
        'college_type_id' => 'nullable|exists:college_types,id',
        'study_duration' => 'nullable|integer|min:1',
        'gender' => 'nullable|in:0,1,2',
        'branch_id' => 'nullable|exists:branches,id',
        'departments' => 'nullable|array',
        'departments.*' => 'exists:departments,id',
        'admissions' => 'nullable|array',
        'admissions.*.id' => 'nullable|exists:admissions,id',
        'admissions.*.year' => 'nullable|integer|min:2000',
        'admissions.*.min_average' => 'nullable|numeric|min:0|max:100',
        'admissions.*.min_total' => 'nullable|numeric|min:0',
        'admissions.*.preference_score' => 'nullable|numeric|min:0',
    ];
}

    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * Throws an HttpResponseException and returns a JSON response with:
     * - status: 'error'
     * - message: general message in English
     * - errors: details of fields that failed validation
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
