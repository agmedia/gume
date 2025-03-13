<?php

namespace App\Helpers;


class Metatags
{

    public static function noFollow()
    {
        return [
            'name' => 'robots',
            'content' => 'noindex,nofollow'
        ];
    }


    public static function empty()
    {
        return [
            'name' => '',
            'content' => ''
        ];
    }
}
