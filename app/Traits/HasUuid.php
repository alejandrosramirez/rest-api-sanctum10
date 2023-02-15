<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Setup model event hooks.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            $model->uuid = Str::uuid()->toString();
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
