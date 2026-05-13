<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class IndexMountainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'      => ['sometimes', 'string', 'max:120'],
            'province_id' => ['sometimes', 'integer', 'exists:provinces,id'],
            'difficulty'  => ['sometimes', 'string', 'in:easy,moderate,hard,strenuous'],
            'is_active'   => ['sometimes', 'boolean'],
            'sort'        => ['sometimes', 'string', 'in:avg_rating,elevation_masl,name'],
            'order'       => ['sometimes', 'string', 'in:asc,desc'],
            'page'        => ['sometimes', 'integer', 'min:1'],
            'limit'       => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function sort(): string
    {
        return $this->input('sort', 'name');
    }

    public function order(): string
    {
        return $this->input('order', 'asc');
    }

    public function perPage(): int
    {
        return (int) $this->input('limit', 15);
    }
}
