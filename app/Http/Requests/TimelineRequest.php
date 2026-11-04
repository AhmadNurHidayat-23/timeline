<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500']];
    }

    public function attributes(): array
    {
        return ['name' => 'nama timeline', 'description' => 'deskripsi'];
    }
}
