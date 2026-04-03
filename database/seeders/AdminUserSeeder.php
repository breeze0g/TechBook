<?php
// database/seeders/MakeBillyAdminSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MakeBillyAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Try to find user by email
        $user = User::where('email', 'breezemarlon272@gmail.com')->first();

        if ($user) {
            // User exists - make them admin
            $oldRole = $user->role;
            $user->role = 'admin';
            $user->is_active = true;
            $user->save();
            
            $this->command->info('=====================================');
            $this->command->info('✅ USER UPDATED TO ADMIN!');
            $this->command->info('=====================================');
            $this->command->info("Name: {$user->name}");
            $this->command->info("Email: {$user->email}");
            $this->command->info("Old Role: {$oldRole}");
            $this->command->info("New Role: {$user->role}");
            $this->command->info("Status: Active");
            $this->command->info('=====================================');
        } else {
            // User doesn't exist - create as admin
            $user = User::create([
                'name' => 'Billy',
                'email' => 'breezemarlon272@gmail.com',
                'password' => Hash::make('16012218'),
                'role' => 'admin',
                'is_active' => true,
                'profile_pic' => null,
            ]);
            
            $this->command->info('=====================================');
            $this->command->info('✅ NEW ADMIN CREATED!');
            $this->command->info('=====================================');
            $this->command->info("Name: {$user->name}");
            $this->command->info("Email: {$user->email}");
            $this->command->info("Password: 16012218");
            $this->command->info("Role: {$user->role}");
            $this->command->info("Status: Active");
            $this->command->info('=====================================');
        }
    }
}