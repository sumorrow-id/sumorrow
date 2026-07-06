<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'body' => ['nullable', 'required_without_all:images,gif_url', 'string', 'max:5000'],
            'category_tags' => ['required', 'array', 'min:1'],
            'category_tags.*' => ['string', 'in:'.implode(',', Post::CATEGORY_TAGS)],
            'images' => ['nullable', 'required_without_all:body,gif_url', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'gif_url' => ['nullable', 'url', 'max:2048'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required_without_all' => __('community.validation_post_content'),
            'images.required_without_all' => __('community.validation_post_content'),
            'category_tags.required' => __('community.validation_category_tags'),
            'images.*.image' => __('community.validation_image'),
            'images.*.max' => __('community.validation_image_size'),
        ];
    }
}
