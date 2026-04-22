<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHeartbeatBulkRequest extends FormRequest
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
            'heartbeats' => ['required', 'array', 'min:1', 'max:25'],
            'heartbeats.*.time' => ['required', 'numeric'],
            'heartbeats.*.entity' => ['required', 'string', 'max:2048'],
            'heartbeats.*.type' => ['required', 'string', 'in:file,app,domain'],
            'heartbeats.*.project' => ['nullable', 'string', 'max:255'],
            'heartbeats.*.language' => ['nullable', 'string', 'max:120'],
            'heartbeats.*.editor' => ['nullable', 'string', 'max:120'],
            'heartbeats.*.is_write' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = $this->all();
        $heartbeatsInput = $this->input('heartbeats', array_is_list($payload) ? $payload : []);

        $heartbeats = collect($heartbeatsInput)
            ->map(function (array $heartbeat): array {
                if (array_key_exists('timestamp', $heartbeat) && ! array_key_exists('time', $heartbeat)) {
                    $heartbeat['time'] = $heartbeat['timestamp'];
                }

                return $heartbeat;
            })
            ->all();

        $this->merge(['heartbeats' => $heartbeats]);
    }
}
