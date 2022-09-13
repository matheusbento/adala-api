<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();

        echo '> Seeding new User: ' . $user->email . "\n";

        $this->call(OrganizationSeeder::class, false, [
            'user' => $user,
        ]);   
        // \App\Models\User::factory(10)->create();
    }
}
