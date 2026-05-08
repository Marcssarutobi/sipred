<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'fullname'          => 'Administrateur',
                'email'             => 'admin@sipred.com',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'fullname'          => 'Jean Gérant',
                'email'             => 'gerant@sipred.com',
                'password'          => Hash::make('password'),
                'role'              => 'gerant',
                'email_verified_at' => now(),
            ],
            [
                'fullname'          => 'Marie Magasinière',
                'email'             => 'magasinier@sipred.com',
                'password'          => Hash::make('password'),
                'role'              => 'magasinier',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // évite les doublons
                $user
            );
        }
    }
}
