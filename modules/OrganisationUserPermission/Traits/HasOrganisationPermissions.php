<?php

namespace Modules\OrganisationUserPermission\Traits;

use Modules\Organisation\Models\Organisation;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasPermissions;
use Exception;

trait HasOrganisationPermissions
{
    use HasPermissions {
        HasPermissions::givePermissionTo as private _givePermissionTo;
        HasPermissions::collectPermissions as private _collectPermissions;
    }

    private int $organisationContextId;
    private Organisation $organisationContext;

    private function setOrganisationContext(int $organisationId = null): void
    {
        $this->organisationContextId = $organisationId;

        if (is_null($this->organisationContextId)) {
            throw new Exception('Organisation ID not set');
        }

        $organisation = Organisation::findOrFail($this->organisationContextId);
    }

    public function givePermissionTo(int $organisationId, ...$permissions)
    {
        $this->setOrganisationContext($organisationId);
        $permissions = $this->collectPermissions(...$permissions);
    }

    private function isInOrganisation(int $organisationId): bool
    {
        return $this->organisations();
    }

    public function collectPermissions(...$permissions)
    {
        return collect($permissions)
            ->flatten()
            ->reduce(function ($array, $permission) {
                if (empty($permission)) {
                    return $array;
                }

                $permission = $this->getStoredPermission($permission);
                if (!$permission instanceof Permission) {
                    return $array;
                }

                $this->ensureModelSharesGuard($permission);

                $array[$permission->getKey()] = PermissionRegistrar::$teams && !is_a($this, Role::class)
                    ? [PermissionRegistrar::$teamsKey => getPermissionsTeamId()] : [];

                return $array;
            }, []);
    }
}
