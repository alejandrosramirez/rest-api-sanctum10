<?php

namespace App\Enums;

/**
 * Generate names of web admin panel roles.
 */
enum WebAdministratorRoles: string
{
    case ADVANCED = 'advanced';
    case MIDDLE = 'middle';
    case BASIC = 'basic';

    public function label(): string {
        return static::getLabel($this);
    }

    protected static function getLabel(self $value): string
    {
        return match($value) {
            WebAdministratorRoles::ADVANCED => 'Avanzado',
            WebAdministratorRoles::MIDDLE => 'Intermedio',
            WebAdministratorRoles::BASIC => 'Básico',
        };
    }
}
