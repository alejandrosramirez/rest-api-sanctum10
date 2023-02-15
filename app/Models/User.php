<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable, HasRoles, SoftDeletes;

    // Other traits
    use HasUuid;

    /**
     * The guard that access on authentication.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

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
        'phone',
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
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('roles', function (Builder $query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
            });
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

    /**
     * Route notifications for the Vonage channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     */
    public function routeNotificationForVonage(Notification $notification): string
    {
        return $this->phone;
    }
}
