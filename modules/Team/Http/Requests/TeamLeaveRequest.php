<?php

namespace Modules\Team\Http\Requests;

use Modules\Team\Http\Rules\UserAssignedToTeam;

class TeamLeaveRequest extends TeamAccessRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['id'][] = new UserAssignedToTeam();
        return $rules;
    }
}
