<?php

namespace Database\Seeders\Api;

use App\Enums\WebRoles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('Asdf123456');

        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => 'Miguel Alejandro',
            'lastname' => 'Salgado Ramírez',
            'phone' => '3330204397',
            'email' => 'alejandrosram@outlook.com',
            'password' => $passwordHash,
            'email_verified_at' => now(),
        ]);
        $user->assignRole(WebRoles::ADMINISTRATOR->value);
    }
}
