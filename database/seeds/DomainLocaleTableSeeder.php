<?php

use Illuminate\Database\Seeder;

class DomainLocaleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('domain_locale')->insert([
            [
                'domain_id' => 1,
                'locale_id' => 37,
            ],
            [
                'domain_id' => 2,
                'locale_id' => 37,
            ],
        ]);
    }
}
