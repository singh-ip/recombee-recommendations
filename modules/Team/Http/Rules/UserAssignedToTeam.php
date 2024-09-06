<?php

namespace Modules\Team\Http\Rules;

use Auth;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Team\Models\Team;

class UserAssignedToTeam implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $userId = Auth::id();
        $assigned = Team::where('id', $value)->whereHas('users', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->exists();
        if (!$assigned) {
            $fail(__('Modules/Team::messages.user_not_in_team'));
        }
    }
}
