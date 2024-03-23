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
        $user->name = 'Ejemplo Usuario';
        $user->email = 'usuario@example.com';
        $user->password = bcrypt('contraseña');
        $user->save();
    }
}
