<?php

namespace Modules\Team\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];

    protected $appends = ['user_count'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_team');
    }

    protected function userCount(): Attribute
    {
        return new Attribute(
            get: fn () => $this->users()->count(),
        );
    }
}
