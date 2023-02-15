<?php

namespace Database\Seeders\Api;

use App\Enums\WebRoles;
use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'web';

        /**
         * Roles Permissions
         */
        Permission::create([
            'name' => 'roles_read',
            'description' => 'Leer roles',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'roles_create',
            'description' => 'Crear roles',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'roles_update',
            'description' => 'Actualizar roles',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'roles_delete',
            'description' => 'Eliminar roles',
            'guard_name' => $guardName,
        ]);

        /**
         * Users Permissions
         */
        Permission::create([
            'name' => 'users_read',
            'description' => 'Leer usuarios',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'users_create',
            'description' => 'Crear usuarios',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'users_update',
            'description' => 'Actualizar usuarios',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'users_delete',
            'description' => 'Eliminar usuarios',
            'guard_name' => $guardName,
        ]);

        /** @var \App\Models\Authorization\Role $role */
        $role = Role::create([
            'name' => WebRoles::ADMINISTRATOR->value,
            'description' => WebRoles::ADMINISTRATOR->label(),
            'guard_name' => $guardName,
        ]);
        $role->givePermissionTo(Permission::where('guard_name', 'web')->get());
    }
}
