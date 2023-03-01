<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\Administrator
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $avatar
 * @property string $name
 * @property string $lastname
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property bool $disabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Authorization\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Authorization\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\AdministratorFactory factory($count = null, $state = [])
 * @method static Builder|Administrator newModelQuery()
 * @method static Builder|Administrator newQuery()
 * @method static Builder|Administrator onlyTrashed()
 * @method static Builder|Administrator permission($permissions)
 * @method static Builder|Administrator query()
 * @method static Builder|Administrator role($roles, $guard = null)
 * @method static Builder|Administrator searchCriteria(string $search)
 * @method static Builder|Administrator whereAvatar($value)
 * @method static Builder|Administrator whereCreatedAt($value)
 * @method static Builder|Administrator whereDeletedAt($value)
 * @method static Builder|Administrator whereDisabled($value)
 * @method static Builder|Administrator whereEmail($value)
 * @method static Builder|Administrator whereEmailVerifiedAt($value)
 * @method static Builder|Administrator whereId($value)
 * @method static Builder|Administrator whereLastname($value)
 * @method static Builder|Administrator whereName($value)
 * @method static Builder|Administrator wherePassword($value)
 * @method static Builder|Administrator whereRememberToken($value)
 * @method static Builder|Administrator whereUpdatedAt($value)
 * @method static Builder|Administrator whereUuid($value)
 * @method static Builder|Administrator withTrashed()
 * @method static Builder|Administrator withoutTrashed()
 * @mixin \Eloquent
 */
class Administrator extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    // Other traits
    use HasUuid;

    /**
     * The guard that access on authentication.
     *
     * @var string
     */
    protected $guard_name = 'web_administrator';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'administrators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'avatar',
        'name',
        'lastname',
        'email',
        'password',
        'disabled'
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
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'disabled' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
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
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('lastname', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('roles', fn (Builder $query) => $query->where('description', 'like', "%{$search}%"));
    }

    /**
     * Avatar mutator/accessor.
     */
    public function avatar(): Attribute
    {
        return Attribute::make(
            fn () => $this->attributes['avatar'] ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&s=200&f=y'
        )->shouldCache();
    }
}
