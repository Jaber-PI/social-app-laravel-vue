<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^(?![_-])[A-Za-z0-9_-]{3,255}$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'The username field is required.',
            'username.string' => 'The username must be a valid string.',
            'username.regex' => 'The username must be between 3 and 255 characters long and can only contain letters, numbers, underscores, and dashes.',
            'username.max' => 'The username may not be greater than 255 characters.',
            'username.unique' => 'The username has already been taken. Please choose a different one.',
        ];
    }
}
