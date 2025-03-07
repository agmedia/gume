<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PagesSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create settings
        //DB::statement(Storage::get(base_path('/database/seeders/settings.txt')));
        
        $items = file_get_contents(base_path('/database/seeders/pages.json'));
        
        if ($items) {
            $items = json_decode($items, true);
            
            foreach ($items as $item) {
                DB::table('pages')->insert($item);
            }
        }
        
    }
}
