<?php

namespace Modules\Team\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Team\Http\Requests\TeamAccessRequest;
use Modules\Team\Http\Requests\TeamAssignmentRequest;
use Modules\Team\Http\Requests\TeamEditRequest;
use Modules\Team\Http\Requests\TeamLeaveRequest;
use Modules\Team\Http\Requests\TeamStoreRequest;
use Modules\Team\Http\Resources\TeamResource;
use Modules\Team\Models\Team;
use Modules\Team\Services\TeamService;

class TeamController extends Controller
{
    use HttpResponse;

    public function index(Request $request, TeamService $teamService): JsonResponse
    {
        return $this->response($teamService->list($request->attributes->get('organisationId'))->toArray());
    }

    public function store(TeamStoreRequest $request, TeamService $teamService): JsonResponse
    {
        return $this->resourceResponse(
            new TeamResource(
                $teamService->create(
                    $request->safe()['name'],
                    $request->attributes->get('organisationId') // if Organisation module is enabled and context exists
                )
            )
        );
    }

    public function show(TeamAccessRequest $request): JsonResponse
    {
        return $this->resourceResponse(new TeamResource(Team::findOrFail($request->validated('id'))));
    }

    public function destroy(TeamAccessRequest $request, TeamService $teamService): JsonResponse
    {
        $result = $teamService->delete($request->validated('id'));
        return $this->response(null, $result['message'], $result['code'] ?? 200);
    }

    public function edit(TeamEditRequest $request, TeamService $teamService): JsonResponse
    {
        $data = $request->validated();
        return $this->resourceResponse(
            new TeamResource($teamService->edit($data['id'], $data['name'])),
            __('Modules/Team::messages.team_edited')
        );
    }

    public function changeUserAssignment(TeamAssignmentRequest $request, TeamService $teamService): JsonResponse
    {
        $data = $request->validated();
        $teamService->changeUserAssignment($data['team'], $data['user'], $data['detach'] ?? false);
        return $this->response(
            null,
            __('Modules/Team::messages.user_' . ($data['detach'] ?? false ? 'removed' : 'added'))
        );
    }

    public function leave(TeamLeaveRequest $request, TeamService $teamService): JsonResponse
    {
        $data = $request->validated();
        $result = $teamService->leaveTeam($data['id']);
        return $this->response(null, $result['message'], $result['code'] ?? 200);
    }

}
