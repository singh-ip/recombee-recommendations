<?php

namespace Modules\Team\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTeam extends Model
{
    use HasFactory;

    protected $table = 'user_team';

    protected $fillable = ['team_id', 'user_id'];

    public function addUserToTeam(int $userId, int $teamId = null): bool
    {
        $teamId = $teamId ?? $this->getDefaultTeam()->id;
        return $this->query()->insert([
            'user_id' => $userId,
            'team_id' => $teamId
        ]);
    }

    public function getDefaultTeam(): Object
    {
        return Team::query()->where('default', true)->first();
    }
}
