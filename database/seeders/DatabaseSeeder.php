<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Api\DatabaseSeeder as ApiDatabaseSeeder;
use Database\Seeders\ApiAdministrator\DatabaseSeeder as ApiAdministratorDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ApiDatabaseSeeder::class,
            ApiAdministratorDatabaseSeeder::class,
        ]);
    }
}
