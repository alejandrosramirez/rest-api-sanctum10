<?php

namespace App\Traits;

use App\Exceptions\GenericException;
use Illuminate\Database\Eloquent\Model;

trait HasRelated
{
    /**
     * Setup model event hooks.
     */
    public static function bootHasRelated(): void
    {
        static::deleting(function (Model $model): void {
            $relateds = $model->relateds;

            foreach ($relateds as $related) {
                if ($model->{$related}()->count() > 0) {
                    throw new GenericException(__('Cannot delete record because it has already in use.'));
                }
            }
        });
    }
}
