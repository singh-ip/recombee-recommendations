<?php

namespace Modules\Organisation\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Modules\Organisation\Models\Organisation;
use Symfony\Component\HttpFoundation\Response;

class SetOrganisationContext
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if ($request->attributes->has('organisationId')) {
            abort(403, 'Organisation context set prematurely.');
        }

        // if running as 'auth' replacement
        if (config('platform.organisation.replace_auth_middleware')) {
            return app(Authenticate::class)->handle($request, function ($request) use ($next) {
                $this->setRequestContext($request);
                return $next($request);
            }, ...$guards);
        }

        // if running as Route middleware, after 'auth'
        if (!Auth::check()) {
            return $next($request);
        }

        $this->setRequestContext($request);

        return $next($request);
    }

    private function setRequestContext(Request &$request): void
    {
        $organisationId = $request->header('X-Organisation-Id');

        if (empty($organisationId)) {
            abort(400, 'Organisation context is required for authenticated requests.');
        }

        if (!is_numeric($organisationId)) {
            abort(400, 'Organisation context format invalid.');
        }

        if (config('platform.organisation.validate_org_context')) {
            if (!$this->validateOrganisationContext($organisationId)) {
                abort(400, 'Invalid Organisation context value.');
            }
        }

        $request->attributes->set('organisationId', $organisationId);
    }

    private function validateOrganisationContext(int $id): bool
    {
        return !is_null(Organisation::find($id));
    }
}
