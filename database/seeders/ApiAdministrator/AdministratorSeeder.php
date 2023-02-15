<?php

namespace Database\Seeders\ApiAdministrator;

use App\Enums\WebAdministratorRoles;
use App\Models\Administrator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('Asdf123456');

        /** @var \App\Models\Administrator $administrator */
        $administrator = Administrator::create([
            'name' => 'Miguel Alejandro',
            'lastname' => 'Salgado Ramírez',
            'email' => 'alejandrosram@outlook.com',
            'password' => $passwordHash,
            'email_verified_at' => now(),
        ]);
        $administrator->assignRole(WebAdministratorRoles::ADVANCED->value);
    }
}
