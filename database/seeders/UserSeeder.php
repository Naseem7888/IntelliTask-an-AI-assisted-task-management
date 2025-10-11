<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test users with consistent, memorable data
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@intellitask.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@intellitask.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@intellitask.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert users into database
        foreach ($users as $userData) {
            User::create($userData);
        }

        // Output success message
        $this->command->info('Created ' . count($users) . ' test users successfully!');
        $this->command->info('Test users created with email/password:');
        foreach ($users as $user) {
            $this->command->info('- ' . $user['email'] . ' / password123');
        }
    }

    /**
     * Create a single test user (helper method for factory integration)
     *
     * @param array $attributes
     * @return User
     */
    public static function createTestUser(array $attributes = [])
    {
        $defaultAttributes = [
            'name' => 'Test User',
            'email' => 'test@intellitask.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => Carbon::now(),
        ];

        return User::create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create multiple test users (helper method for factory integration)
     *
     * @param int $count
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createTestUsers(int $count = 3, array $attributes = [])
    {
        $users = collect();
        
        for ($i = 1; $i <= $count; $i++) {
            $defaultAttributes = [
                'name' => "Test User {$i}",
                'email' => "test{$i}@intellitask.com",
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
            ];

            $users->push(User::create(array_merge($defaultAttributes, $attributes)));
        }

        return $users;
    }

    /**
     * Get default test user credentials for development
     *
     * @return array
     */
    public static function getTestCredentials()
    {
        return [
            [
                'email' => 'john@intellitask.com',
                'password' => 'password123',
                'name' => 'John Doe'
            ],
            [
                'email' => 'jane@intellitask.com',
                'password' => 'password123',
                'name' => 'Jane Smith'
            ],
            [
                'email' => 'mike@intellitask.com',
                'password' => 'password123',
                'name' => 'Mike Johnson'
            ],
        ];
    }
}