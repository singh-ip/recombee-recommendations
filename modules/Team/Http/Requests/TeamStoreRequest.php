<?php

namespace Modules\Team\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\common\Services\ModuleService;

class TeamStoreRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => [
                'string',
                'required',
                'max:' . config('platform.team.max_team_name_length')
            ]
        ];

        // adding unique index on DB level in migration would cause problems when Organisation and Team modules are
        // switched on and off in different combinations, therefore a Rule was made
        if (ModuleService::isEnabled('Organisation')) {
            $rules['name'][] = Rule::unique('teams')->where(
                'organisation_id',
                $this->attributes->get('organisationId')
            );
            return $rules;
        }

        $rules['name'][] = Rule::unique('teams')->where(function ($query) {
            return $query->whereNull('deleted_at');
        });

        return $rules;
    }
}
