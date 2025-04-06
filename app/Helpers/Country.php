<?php


namespace App\Helpers;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Country
{

    /**
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function list(): Collection
    {
        $countries = Storage::disk('assets')->get('country.json');

        return collect(json_decode($countries, true));
    }

}
