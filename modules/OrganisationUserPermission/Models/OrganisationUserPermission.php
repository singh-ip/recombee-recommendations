<?php

namespace Modules\OrganisationUserPermission\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\common\Exceptions\ModuleNotEnabledException;
use Modules\common\Exceptions\RequirementsNotMetException;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Models\Organisation;
use Spatie\Permission\Models\Permission;

class OrganisationUserPermission extends Model
{
    protected $fillable = [
        'organisation_id',
        'user_id',
        'permission_id'
    ];

    public function __construct(array $attributes = [])
    {
        if (!ModuleService::isEnabled('Organisation')) {
            throw new ModuleNotEnabledException('Organisation');
        }

        if (!collect(app()->getLoadedProviders())->has('Spatie\Permission\PermissionServiceProvider')) {
            throw new RequirementsNotMetException('Spatie Permisions package');
        }

        parent::__construct($attributes);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
