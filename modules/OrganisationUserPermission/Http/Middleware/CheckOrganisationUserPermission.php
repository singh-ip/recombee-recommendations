<?php

namespace Modules\OrganisationUserPermission\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Modules\Organisation\Models\Organisation;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganisationUserPermission
{
    public function handle(Request $request, Closure $next, $permission, $guard = null): Response
    {
        $authGuard = app('auth')->guard($guard);

        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $organisationId = $request->attributes->get('organisationId');
        $this->checkOrganisationContext($organisationId);
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        if (Auth::user()->organisationUserPermissions()
            ->whereHas('permission', function ($query) use ($permissions) {
                $query->whereIn('name', $permissions);
            })
            ->where('organisation_id', $organisationId)
            ->exists()) {
            return $next($request);
        }

        throw UnauthorizedException::forPermissions($permissions);
    }

    private function checkOrganisationContext(int $organisationId)
    {
        $organisation = Organisation::find($organisationId);

        if (!is_null($organisation)) {
            abort(400, 'Organisation context invalid.');
        }

        if (!$organisation->users()->has(Auth::user())) {
            abort(403, 'Organisation context forbidden.');
        }
    }
}
