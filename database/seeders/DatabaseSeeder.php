<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    

    public function run(): void
{
    User::create([
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
    $this->call(UserSeeder::class);
}
}
