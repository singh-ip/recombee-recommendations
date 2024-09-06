<?php

namespace Modules\Team\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamAssignmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'team' => ['required', 'integer', Rule::exists('teams', 'id')],
            'user' => ['required', 'integer', Rule::exists('users', 'id')
                ->whereNotNull('email_verified_at')],
            'detach' => ['sometimes', 'bool'],
        ];
    }
}
