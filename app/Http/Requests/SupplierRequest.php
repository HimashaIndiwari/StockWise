<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Create/update validation for suppliers (admin only). */
class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(['name', 'email', 'phone', 'address'])
            ->filter(fn ($field) => is_string($this->input($field)))
            ->mapWithKeys(fn ($field) => [$field => trim($this->input($field)) === '' ? null : trim($this->input($field))])
            ->all());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+()\-\s.]{6,50}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'The phone number may contain only digits, spaces and + ( ) - . characters (at least 6).'];
    }
}
