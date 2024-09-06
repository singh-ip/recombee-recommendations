<?php

namespace Modules\Team\Services;

use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Models\Organisation;
use Symfony\Component\HttpFoundation\Response;
use Modules\Team\Models\Team;

class TeamService
{
    public function list(int $organisationId = null): Collection
    {
        return Team::query()->orderBy('name')
            ->when(
                ModuleService::isEnabled('Organisation') && is_numeric($organisationId),
                function (Builder $query) use ($organisationId) {
                    return $query->where('organisation_id', $organisationId);
                }
            )->get();
    }

    public function create(string $name, int $organisationId = null): Team
    {
        $team = new Team(['name' => $name]);

        if (ModuleService::isEnabled('Organisation')) {
            if (!config('platform.organisation.validate_org_context')) {
                Organisation::findOrFail($organisationId);
            }

            $team->organisation_id = $organisationId;
        }

        $team->save();

        return $team;
    }

    public function delete(int $teamId): array
    {
        $team = Team::find($teamId);
        if (!$team) {
            return [
                'message' => __('Modules/Team::messages.invalid_team_id'),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ];
        }
        if (!$team->users->isEmpty()) {
            return [
                'message' => __('Modules/Team::messages.team_not_empty', ['team_name' => $team->name]),
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }
        if($team->default) {
            return [
                'message' => __('Modules/Team::messages.cannot_delete_default_team'),
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }
        $team->delete();
        return [
            'message' => __('Modules/Team::messages.team_deleted', ['team_name' => $team->name]),
        ];
    }

    public function edit(int $teamId, string $name): Team
    {
        $team = Team::findOrFail($teamId);
        $team->name = $name;
        $team->save();
        return $team;
    }

    public function changeUserAssignment(int $teamId, int $userId, ?bool $detach = false): void
    {
        $team = Team::findOrFail($teamId);
        $user = User::findOrFail($userId);

        if ($detach) {
            $team->users()->detach($user);
            return;
        }
        $team->users()->syncWithoutDetaching([$user->id]);
    }

    public function leaveTeam(int $teamId): array
    {
        $user = Auth::user();
        if($user->teams()->count() === 1) {
            return [
                'message' => __('Modules/Team::messages.user_not_in_other_teams'),
                'code' => 400
            ];
        }

        $this->changeUserAssignment($teamId, $user->id, true);
        return [
            'message' => __('Modules/Team::messages.user_removed'),
        ];
    }
}
