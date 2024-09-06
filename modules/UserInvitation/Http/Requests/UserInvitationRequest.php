<?php

namespace  Modules\UserInvitation\Http\Requests;

use App\Models\User;
use Modules\Team\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserInvitationRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $teamIdDefault = Team::query()->where('default', true)->first()['id'];
        $this->merge([
            'email' => strtolower($this->email),
            'team_id' => $this->team_id ?? $teamIdDefault,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:' . User::class],
            'role' => ['required', Rule::exists('roles', 'name')],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ];
    }
}
