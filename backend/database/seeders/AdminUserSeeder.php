<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pass =

        User::create([
            'name' => 'Alex Ban',
            'email' => 'alexbanut10@gmail.com',
            'password' => Hash::make('AlexBn1441!'),
            'avatar_url' => null,
            'bio' => 'Hi my name is Alex, I\'m the CTO of Socialize AI',
            'website' => null,
            'location' => 'Sibiu, Romania',
            'cover_image_url' => null,
        ]);
    }
}
