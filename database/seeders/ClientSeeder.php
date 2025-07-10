<?php

namespace Database\Seeders;

use App\Models\Clients\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have at least one user to assign as creator
        if (User::count() === 0) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);
        }

        // Get existing users to randomly assign as creators
        $users = User::all();

        // Create 100 clients
        Client::factory()
            ->count(100)
            ->make()
            ->each(function ($client) use ($users) {
                $client->created_by_id = $users->random()->id;
                $client->save();
            });

        $this->command->info('Successfully created 100 clients');
    }
} 