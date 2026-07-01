<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            // 勤怠管理で使いそうな部署名をランダムにセット
            'department' => fake()->randomElement(['開発部', '営業部', '総務部', '人事部']),
            'password' => static::$password ??= Hash::make('password'), // 初期パスワードは「password」になります
        ];
    }
}