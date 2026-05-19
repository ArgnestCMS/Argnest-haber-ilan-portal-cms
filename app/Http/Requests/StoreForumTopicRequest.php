<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'forum_category_id' => ['required', 'integer', 'exists:forum_categories,id'],
            'title' => ['required', 'string', 'min:5', 'max:180'],
            'content' => ['required', 'string', 'min:20', 'max:12000'],
            'tag_names' => ['nullable', 'string', 'max:300'],
            'media_asset_ids' => ['nullable', 'array', 'max:8'],
            'media_asset_ids.*' => ['integer', 'exists:media_assets,id'],
        ];
    }
}
