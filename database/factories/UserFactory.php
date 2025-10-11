<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a specific password.
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Create a user with a specific email domain.
     */
    public function withEmailDomain(string $domain): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->userName() . '@' . $domain,
        ]);
    }

    /**
     * Create a user with a specific name pattern.
     */
    public function withName(string $firstName = null, string $lastName = null): static
    {
        $firstName = $firstName ?? fake()->firstName();
        $lastName = $lastName ?? fake()->lastName();
        
        return $this->state(fn (array $attributes) => [
            'name' => $firstName . ' ' . $lastName,
        ]);
    }

    /**
     * Create a test user with predictable data for development.
     */
    public function testUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create an admin test user.
     */
    public function adminUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Admin User',
            'email' => 'admin@intellitask.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create a demo user for showcasing features.
     */
    public function demoUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Demo User',
            'email' => 'demo@intellitask.com',
            'password' => Hash::make('demo123'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create users with realistic professional names.
     */
    public function professional(): static
    {
        $titles = ['Dr.', 'Mr.', 'Ms.', 'Mrs.'];
        $title = fake()->randomElement($titles);
        
        return $this->state(fn (array $attributes) => [
            'name' => $title . ' ' . fake()->firstName() . ' ' . fake()->lastName(),
        ]);
    }

    /**
     * Create users with company email addresses.
     */
    public function corporate(): static
    {
        $domains = ['company.com', 'business.org', 'enterprise.net', 'corp.com'];
        
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->userName() . '@' . fake()->randomElement($domains),
        ]);
    }

    /**
     * Create users that were recently verified.
     */
    public function recentlyVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create users with longer names for testing UI.
     */
    public function longName(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->firstName() . ' ' . fake()->lastName() . '-' . fake()->lastName(),
        ]);
    }
}