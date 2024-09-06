<?php

namespace Modules\Team\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamEditRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id')
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            (new TeamStoreRequest())->rules(),
            (new TeamAccessRequest())->rules()
        );
    }
}
