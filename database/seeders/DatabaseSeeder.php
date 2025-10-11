<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Only run seeders in development or local environment
        if (!App::environment(['local', 'development', 'testing'])) {
            $this->command->error('Seeders can only be run in local, development, or testing environments!');
            return;
        }

        $this->command->info('Starting database seeding...');

        try {
            // Disable foreign key checks for clean truncation
            Schema::disableForeignKeyConstraints();

            // Truncate tables for clean state (in reverse order of dependencies)
            $this->command->info('Cleaning existing data...');
            DB::table('tasks')->delete();
            DB::table('users')->delete();

            // Re-enable foreign key checks
            Schema::enableForeignKeyConstraints();

            $this->command->info('Existing data cleaned successfully.');

            // Seed users first (tasks depend on users)
            $this->command->info('Seeding users...');
            $this->call(UserSeeder::class);
            $this->command->info('Users seeded successfully.');

            // Seed tasks (depends on users)
            $this->command->info('Seeding tasks...');
            $this->call(TaskSeeder::class);
            $this->command->info('Tasks seeded successfully.');

            $this->command->info('Database seeding completed successfully!');

        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            Schema::enableForeignKeyConstraints();
            
            $this->command->error('Database seeding failed: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
            
            throw $e;
        }
    }
}
