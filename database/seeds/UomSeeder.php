<?php

use Illuminate\Database\Seeder;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('uoms')->insert([
            ['name' => 'pcs',     'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'unit',    'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'set',     'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pack',    'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'jerigen', 'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'box',     'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'roll',    'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kg',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'gram',    'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mg',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ton',     'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ml',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'liter',   'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'meter',   'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cm',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mm',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'm2',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'm3',      'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lusin',   'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lembar',  'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pasang',  'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'bag',     'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'botol',   'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pallet',  'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pail',    'created_by' => 'superadmin', 'updated_by' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}