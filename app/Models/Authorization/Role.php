<?php

namespace App\Models\Authorization;

use App\Models\Administrator;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as RoleModel;

class Role extends RoleModel
{
    use HasFactory, SoftDeletes;

    // Other traits
    use HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'guard_name',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string, string>
     */
    protected $dates = ['deleted_at'];

    /**
     * Scope a query with search.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     */
    public function scopeSearchCriteria(Builder $query, string $search): Builder
    {
        return $query->where('description', 'like', "%{$search}%");
    }

    /**
     * A role belongs to some administrators of the model associated with its guard.
     */
    public function administrators(): BelongsToMany
    {
        return $this->morphedByMany(
            Administrator::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        );
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        );
    }
}
