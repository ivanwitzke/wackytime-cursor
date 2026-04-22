<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHeartbeatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'time' => ['required', 'numeric'],
            'entity' => ['required', 'string', 'max:2048'],
            'type' => ['required', 'string', 'in:file,app,domain'],
            'project' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:120'],
            'editor' => ['nullable', 'string', 'max:120'],
            'is_write' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('timestamp') && ! $this->has('time')) {
            $this->merge(['time' => $this->input('timestamp')]);
        }
    }
}
