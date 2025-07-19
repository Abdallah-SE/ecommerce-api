<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use Modules\User\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password123'), // default password
            'remember_token' => Str::random(10),

        ];
    }
}
