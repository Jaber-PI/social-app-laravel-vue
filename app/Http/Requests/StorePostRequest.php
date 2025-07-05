<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['string', 'nullable', 'max:255'],
            'attachments' => ['array', 'max:10'], // Must be an array of files
            'attachments.*' => [
                'file',                             // Each item must be a file
                'mimes:jpg,jpeg,png,pdf,doc,docx.mp3,mp4',  // Allowed file types
                'max:2048',                         //
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'body' => $this->input('body') ?: '',
            'attachments' => $this->input('attachments', []),
        ]);
    }

    public function messages()
    {
        return [
            // For the array of attachments
            'attachments.array' => 'Attachments must be an array of files.',
            // For each file
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.max' => 'Each attachment must not exceed 2MB.',
            'attachments.*.mimes' => 'Only JPG, PNG, and PDF files are allowed.',
        ];
    }
}
