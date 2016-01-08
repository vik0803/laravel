<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(LocalesTableSeeder::class);
        $this->call(DomainsTableSeeder::class);
        $this->call(DomainLocaleTableSeeder::class);

        Model::reguard();
    }
}
