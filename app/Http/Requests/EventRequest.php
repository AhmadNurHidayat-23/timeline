<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:160'],
            'event_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:0001-01-01', 'before_or_equal:9999-12-31'],
            'description' => ['nullable', 'string', 'max:1000']];
    }

    public function attributes(): array
    {
        return ['title' => 'judul peristiwa', 'event_date' => 'tanggal', 'description' => 'deskripsi'];
    }
}
