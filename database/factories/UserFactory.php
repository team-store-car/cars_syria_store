<?php

namespace Database\Factories;

use App\Models\User; // <--- تأكد من استيراد المودل الصحيح هنا
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
            // 'email_verified_at' => now(), // يمكنك إضافة هذا إذا أردت أن يكون الإيميل موثقاً افتراضياً
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Configure the factory to assign a role after creating a user.
     */
    public function withRole(string $role): static
    {
        return $this->afterCreating(function (User $user) use ($role) { // <--- تأكد أن User هنا تشير إلى App\Models\User (بفضل الـ use في الأعلى)
            $user->assignRole($role);
        });
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
}
