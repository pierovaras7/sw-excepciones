<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'admin';
        $user->email = 'admin@gmail.com';
        $user->password = bcrypt('contraseña');
        $user->save();

        $user = new User();
        $user->name = 'Jair V';
        $user->email = 'jvasquez@gmail.com';
        $user->password = bcrypt('contraseña');
        $user->save();
    }
}
