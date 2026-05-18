<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:5', 'max:8000'],
            'parent_id' => ['nullable', 'integer', 'exists:forum_posts,id'],
            'quoted_post_id' => ['nullable', 'integer', 'exists:forum_posts,id'],
        ];
    }
}
