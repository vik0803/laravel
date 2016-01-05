<?php

use Illuminate\Database\Seeder;

class DomainsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('domains')->insert([
            [
                'name' => 'Default Public Website',
                'slug' => 'www',
                'default_locale_id' => 37,
            ],
            [
                'name' => 'CMS (Admin Control Panel)',
                'slug' => 'cms',
                'default_locale_id' => 37,
            ],
        ]);
    }
}
