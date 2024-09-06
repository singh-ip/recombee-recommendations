<?php

namespace Modules\Organisation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\common\Exceptions\ModuleNotEnabledException;
use Modules\common\Services\ModuleService;
use Modules\Team\Models\Team;

class Organisation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function __construct(array $attributes = [])
    {
        if (!ModuleService::isEnabled('Team')) {
            throw new ModuleNotEnabledException('Team');
        }

        parent::__construct($attributes);
    }

    protected $fillable = [
        'name'
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function users(): Collection
    {
        $teams = $this->load('teams.users');
        $users = collect();
        foreach($teams->teams as $team) {
            $users = $users->merge($team->users);
        }
        return $users->unique('id')->values();
    }
}
