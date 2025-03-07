<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Un Guard model
        Model::unguard();
        
        $this->command->call('migrate:fresh');
        
        $this->command->info('Refreshing database...');
        $this->command->info('Preparing seeders...!');
        
        $this->call(UserSedder::class);
        $this->call(BouncerSedder::class);
        $this->call(SettingsSedder::class);
        $this->call(PagesSedder::class);
        
        $this->command->comment('Master users created!');
        $this->command->comment('Roles, abilities & permissions created!');
        $this->command->comment('Default settings created!');
        $this->command->comment('Default pages created!');
        
        $this->command->newLine();
        $this->command->info('Enjoy your app!');
        $this->command->comment('...');
        
        // ReGuard model
        Model::reguard();
    }
}
