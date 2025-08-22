<?php

namespace App\Http\Requests;

use App\Models\GroupUser;
use App\Models\User;
use App\Rules\ExistsUsernameOrEmail;
use Illuminate\Foundation\Http\FormRequest;

class GroupInviteMemberRequest extends FormRequest
{


    public ?User $invitee = null;


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
            'email' => ['required', 'email', function ($attribute, $value, $fail) {
                $user = User::where('email', $value)
                    ->orWhere('username', $value)
                    ->first();

                if (!$user) {
                    $fail("There is no user with this $attribute.");
                }

                if ($this->route('group')->members()->where('email', $value)->exists()) {
                    $fail("User is already a member.");
                }

                $this->invitee = $user;
            }],
        ];
    }
}
