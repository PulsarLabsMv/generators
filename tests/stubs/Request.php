<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PulsarLabs\Generators\Tests\TestSupport\Support\PostStatuses;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        $rules = [
            'title'        => ['string', 'max:255',],
            'slug'         => ['string', 'max:255',],
            'excerpt'      => ['nullable', 'string',],
            'content'      => ['nullable', 'string',],
            'status'       => ['string', 'max:255', Rule::enum(PostStatuses::class),],
            'published_at' => ['nullable', 'date',],
            'category'     => ['nullable', 'exists:categories,id',],
            'tags'         => ['nullable', 'array',],
            'tags.*'       => ['exists:tags,id',],
        ];

        $slug_unique = Rule::unique('posts', 'slug');

        if ($post) {
            $rules['slug'][] = $slug_unique->ignore($post->id);

            return $rules;
        }

        $rules['slug'][] = $slug_unique;
        $rules['title'][] = 'required';
        $rules['slug'][] = 'required';
        $rules['status'][] = 'required';

        return $rules;
    }
}
