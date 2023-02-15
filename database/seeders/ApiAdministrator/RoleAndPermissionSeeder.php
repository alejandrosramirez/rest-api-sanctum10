<?php

namespace Database\Seeders\ApiAdministrator;

use App\Enums\WebAdministratorRoles;
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

        $guardName = 'web_administrator';

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
         * Administrators Permissions
         */
        Permission::create([
            'name' => 'administrators_read',
            'description' => 'Leer administradores',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'administrators_create',
            'description' => 'Crear administradores',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'administrators_update',
            'description' => 'Actualizar administradores',
            'guard_name' => $guardName,
        ]);
        Permission::create([
            'name' => 'administrators_delete',
            'description' => 'Eliminar administradores',
            'guard_name' => $guardName,
        ]);

        /** @var \App\Models\Authorization\Role $role */
        $role = Role::create([
            'name' => WebAdministratorRoles::ADVANCED->value,
            'description' => WebAdministratorRoles::ADVANCED->label(),
            'guard_name' => $guardName,
        ]);
        $role->givePermissionTo(Permission::where('guard_name', 'web_admin')->get());

        /** @var \App\Models\Authorization\Role $role */
        $role = Role::create([
            'name' => WebAdministratorRoles::MIDDLE->value,
            'description' => WebAdministratorRoles::MIDDLE->label(),
            'guard_name' => $guardName,
        ]);
        $role->givePermissionTo(
            Permission::where('guard_name', 'web_admin')
                ->where('name', '!=', 'roles_delete')
                ->where('name', '!=', 'administrators_delete')
                ->get()
        );

        /** @var \App\Models\Authorization\Role $role */
        $role = Role::create([
            'name' => WebAdministratorRoles::BASIC->value,
            'description' => WebAdministratorRoles::BASIC->label(),
            'guard_name' => $guardName,
        ]);
        $role->givePermissionTo(
            Permission::where('guard_name', 'web_admin')
                ->where('name', '==', 'roles_read')
                ->where('name', '==', 'administrators_read')
                ->get()
        );
    }
}
