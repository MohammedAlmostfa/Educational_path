<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'average'   => 'required|numeric|min:0|max:100',
            'gender'    => 'required|in:0,1', // 0 أو 1 فقط
            'branch_id' => 'required|exists:branches,id',
        ];
    }

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
     * تعديل رسالة الفشل
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'فشل التحقق من صحة البيانات',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
