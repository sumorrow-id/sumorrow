<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMountainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'name' => ['required', 'string', 'max:255'],
            'elevation_masl' => ['required', 'integer', 'min:0', 'max:9000'],
            'length_km' => ['required', 'numeric', 'min:0'],
            'elevation_gain_m' => ['required', 'integer', 'min:0'],
            'coordinates' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'difficulty' => ['required', 'in:easy,moderate,hard,strenuous'],
            'is_active' => ['nullable', 'boolean'],
            'closed_since' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $isActive = $this->boolean('is_active');

        $this->merge([
            'is_active' => $isActive,
            'closed_since' => $isActive ? null : $this->input('closed_since'),
        ]);
    }
}
