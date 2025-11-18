<?php

namespace Database\Seeders\Config;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       if (!Schema::hasTable('countries'))
       {
           $this->command->info('running countries seeder');
           DB::unprepared( file_get_contents(database_path('sql/countries.sql')));
           $this->command->info('Countries seeder completed');
       }

    }
}
