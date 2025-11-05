<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seeders = [
            LocationSeeder::class,
            RolePermissionSeeder::class,
            SuperadminUserSeeder::class,
            UomSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $this->call($seeder);
        }
    }
}