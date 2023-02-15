<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

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
}
