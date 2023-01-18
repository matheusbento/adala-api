<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator;

class DatabaseSeeder extends Seeder
{
    private Generator $faker;

    const EMAIL_ORGANIZATION = 'organization@example.com';

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::whereEmail(self::EMAIL_ORGANIZATION)->update(['email' => $this->faker->email]);
        $user = User::factory(['email' => self::EMAIL_ORGANIZATION])->create();

        echo '> Seeding new User: ' . $user->email . "\n";

        $this->call(OrganizationSeeder::class, false, [
            'user' => $user,
        ]);
    }
}
