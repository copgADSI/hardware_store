<?php

namespace Database\Seeders\role;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    const ROLES = [
        'customer',
        'admin',
        'customer vip'
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 0; $i < count(self::ROLES); $i++) {
            $role = Role::where('role', self::ROLES[$i]);
            if ($role->first()) continue;
            Role::create(['role' => self::ROLES[$i]]);
        }
    }
}
