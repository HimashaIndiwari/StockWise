<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Admin-only create/update validation for user accounts.
 * Password is required on create and optional on update.
 */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        $editing = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($editing)],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'status' => ['required', 'string', Rule::in(User::STATUSES)],
            'password' => [$editing ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ];
    }
}
