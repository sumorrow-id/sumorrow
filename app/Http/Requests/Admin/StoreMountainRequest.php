<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMountainRequest extends FormRequest
{
    /**
     * DMS coordinate pair, e.g. `8 deg 16' 0" S, 115 deg 25' 0" E`.
     * Must stay parseable by WeatherController::parseCoordinates(), which
     * otherwise silently falls back to default coordinates.
     */
    public const COORDINATES_PATTERN = '/^\s*\d{1,2}\s*deg\s*\d{1,2}\s*\'\s*\d{1,2}(?:\.\d+)?\s*"\s*[NS]\s*,\s*\d{1,3}\s*deg\s*\d{1,2}\s*\'\s*\d{1,2}(?:\.\d+)?\s*"\s*[EW]\s*$/i';

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
            'coordinates' => ['required', 'string', 'max:255', 'regex:'.self::COORDINATES_PATTERN],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:4096'],
            'difficulty' => ['required', 'in:easy,moderate,hard,strenuous'],
            'is_active' => ['nullable', 'boolean'],
            'closed_since' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'coordinates.regex' => __('admin.coordinates_format_error'),
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
