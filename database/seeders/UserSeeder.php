<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
       

        // Viewer/User
        User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Viewer',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
