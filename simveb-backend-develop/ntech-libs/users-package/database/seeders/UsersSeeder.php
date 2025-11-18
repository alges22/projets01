<?php


namespace Ntech\UserPackage\Database\Seeders;

use Database\Seeders\SimpleUserSeeder;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StaffSeeder::class);
        $this->call(SimpleUserSeeder::class);
    }
}
