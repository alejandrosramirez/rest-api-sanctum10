<?php

namespace App\Enums;

/**
 * Generate names of web panel roles.
 */
enum WebRoles: string
{
    case ADMINISTRATOR = 'administrator';

    public function label(): string {
        return static::getLabel($this);
    }

    protected static function getLabel(self $value): string
    {
        return match($value) {
            WebRoles::ADMINISTRATOR => 'Administrador',
        };
    }
}
