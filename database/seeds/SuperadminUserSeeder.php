<?php

use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'username' => 'superadmin',
            'password' => Hash::make('superadmin'),
            'name' => 'Super Admin',
            'location_code' => 'it',
            'api_token' => Str::random(60),
            'created_by' => 'superadmin',
            'updated_by' => 'superadmin',
        ]);

        // Assign role super admin ke user
        $user->assignRole('super admin');
    }
}