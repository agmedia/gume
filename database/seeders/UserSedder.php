<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create admins
        DB::insert(
            "INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
              ('Filip Jankoski', 'filip@agmedia.hr', null, '" . bcrypt('majamaja001') . "', '', 0, '', NOW(), NOW()),
              ('Tomislav Jureša', 'tomislav@agmedia.hr', null, '" . bcrypt('bakanal') . "', '', 0, '', NOW(), NOW())"
        );

        // create admins details
        DB::insert(
            "INSERT INTO `user_details` (`user_id`, `fname`, `lname`, `address`, `zip`, `city`, `avatar`, `bio`, `social`, `role`, `status`, `created_at`, `updated_at`) VALUES
              (1, 'Filip', 'Jankoski', 'Kovačića 23', '44320', 'Kutina', 'media/avatars/avatar0.jpg', 'Lorem ipsum...', '790117367', 'admin', 1, NOW(), NOW()),
              (2, 'Tomislav', 'Jureša', 'Malešnica bb', '10000', 'Zagreb', 'media/avatars/avatar0.jpg', 'Lorem ipsum...', '', 'admin', 1, NOW(), NOW())"
        );
    }
}
