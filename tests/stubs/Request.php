<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Support\Enums\PostStatuses;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'        => ['string', 'max:255'],
            'slug'         => ['string', 'max:255'],
            'excerpt'      => ['nullable', 'string', 'max:65535'],
            'content'      => ['nullable', 'string',],
            'status'       => [Rule::enum(PostStatuses::class)],
            'published_at' => ['nullable', 'date'],
            'category'     => ['nullable', 'exists:categories,id'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['exists:tags,id'],
        ];

        $uniqueSlug = Rule::unique('posts', 'slug');

        if ($post = $this->route('post')) {
            $rules['slug'][] = $uniqueSlug->ignore($post->id);
        } else {
            $rules['slug'][] = $uniqueSlug;
            $rules['slug'][] = 'required';
            $rules['name'][] = 'required';
            $rules['status'][] = 'required';
        }

        return $rules;
    }
}
