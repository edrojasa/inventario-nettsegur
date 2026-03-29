<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'notes' => 'string|nullable',
            'assigned_to' => 'nullable|integer|exists:users,id,deleted_at,NULL',
            'asset_id' => 'nullable|integer|exists:assets,id,deleted_at,NULL',
            'assigned_location' => 'nullable|integer|exists:locations,id,deleted_at,NULL',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $n = (int) $this->filled('assigned_to')
                + (int) $this->filled('asset_id')
                + (int) $this->filled('assigned_location');
            if ($n !== 1) {
                $validator->errors()->add(
                    'assigned_to',
                    trans('validation.required', ['attribute' => trans('admin/hardware/form.checkout_to')])
                );
            }
        });
    }
}
