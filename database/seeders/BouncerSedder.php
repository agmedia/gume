<?php

namespace Database\Seeders;

use App\Models\Helper;
use App\Models\User;
use Illuminate\Database\Seeder;
use Bouncer;

class BouncerSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*******************************************************************************
        *                                Copyright : AGmedia                           *
        *                              email: filip@agmedia.hr                         *
        *******************************************************************************/
        //
        // ROLES
        //
        // MASTER
        $master = Bouncer::role()->firstOrCreate([
            'name' => 'master',
            'title' => 'Super-admin',
        ]);

        // ADMIN
        $admin = Bouncer::role()->firstOrCreate([
            'name' => 'admin',
            'title' => 'Administrator',
        ]);

        // EDITOR
        $editor = Bouncer::role()->firstOrCreate([
            'name' => 'editor',
            'title' => 'Editor',
        ]);

        // CUSTOMER
        $customer = Bouncer::role()->firstOrCreate([
            'name' => 'customer',
            'title' => 'Kupac',
        ]);

        /*******************************************************************************
        *                                Copyright : AGmedia                           *
        *                              email: filip@agmedia.hr                         *
        *******************************************************************************/
        //
        // ABILITIES
        /**/
        $view_dashboard = Bouncer::ability()->firstOrCreate([
            'name' => 'view-dashboard',
            'title' => 'Pregled nadzorne ploče'
        ]);

        /*******************************************************************************
         *                                Copyright : AGmedia                           *
         *                              email: filip@agmedia.hr                         *
         *******************************************************************************/
        //
        // PERMISSIONS
        
        // MASTER
        Bouncer::allow($master)->everything();
        
        /**
         * ADMIN
         */
        Bouncer::allow($admin)->to($view_dashboard);
        
        /**
         * EDITOR
         */
        Bouncer::allow($editor)->to($view_dashboard);
        
        /**
         * CUSTOMER
         */
        Bouncer::allow($customer)->to($view_dashboard);
        


        /**
         *
         */
        $users = User::whereIn('id', [1, 2])->get();

        foreach ($users as $user) {
            $user->assign('master');
        }
    }
}
