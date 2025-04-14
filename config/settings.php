<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'free_shipping' => 700,

    'pagination' => [
        'front' => 60,
        'back'  => 60
    ],

    'search_keyword'    => 'pojam',
    'brand_path'        => 'brand',
    'publisher_path'    => 'nakladnik',
    'group_path'        => 'webshop',

    'unknown_brand'     => 0,
    'unknown_publisher' => 376,
    'images_domain'     => env('APP_IMAGE_DOMAIN'),
    'default_tax_id'    => 1,
    'eur_divide_amount' => 0.13272280,

    'sorting_list' => [
        0 => [
            'title' => 'Zadnje dodano',
            'value' => 'created_at-desc',
            'sort_order' => 1
        ],
        1 => [
            'title' => 'Najpopularnije',
            'value' => 'viewed-desc',
            'sort_order' => 2
        ],
        2 => [
            'title' => 'Cijena (niža - viša)',
            'value' => 'price-asc',
            'sort_order' => 3
        ],
        3 => [
            'title' => 'Cijena (viša - niža)',
            'value' => 'price-desc',
            'sort_order' => 4
        ],
        4 => [
            'title' => 'A - Ž',
            'value' => 'name-asc',
            'sort_order' => 5
        ],
        5 => [
            'title' => 'Ž - A',
            'value' => 'name-desc',
            'sort_order' => 6
        ],
    ],

    'sezone' => [
        0 => [
            'title' => 'Ljetne gume',
            'key' => 'Ljeto'
        ],
        1 => [
            'title' => 'Zimske gume',
            'key' => 'Zima'
        ],
        2 => [
            'title' => 'Cjelogodišnje gume',
            'key' => 'Sve'
        ],
    ],

    'actions_sorting_list' => [
        0 => [
            'title' => 'Sve akcije',
            'type' => 'all',
            'value' => 0
        ],
        1 => [
            'title' => 'Zaključane',
            'type' => 'lock',
            'value' => 'da'
        ],
        2 => [
            'title' => 'Otključane',
            'type' => 'lock',
            'value' => 'ne'
        ],
        3 => [
            'title' => 'Sa kuponom',
            'type' => 'coupon',
            'value' => 'da'
        ],
        4 => [
            'title' => 'Bez kupona',
            'type' => 'coupon',
            'value' => 'ne'
        ],
        5 => [
            'title' => 'Aktivne',
            'type' => 'status',
            'value' => 'da'
        ],
        6 => [
            'title' => 'Neaktivne',
            'type' => 'status',
            'value' => 'ne'
        ],
        7 => [
            'title' => 'Samo jedan artikl',
            'type' => 'group',
            'value' => 'single'
        ],
    ],

    'order' => [
        'made_text' => 'Narudžba napravljena.',
        'status'    => [
            'new'        => 1,
            'unfinished' => 8,
            'declined'   => 7,
            'canceled'   => 5,
            'paid'       => 3,
            'send'       => 4,
            'ready'      => 10,
        ],
        // Can be number or array.
        'new_status' => 1,
        'canceled_status' => [7, 5],
    ],

    'reservation_statuses' => [2, 3, 5, 9],

    'special_action' => [
        'title' => 'Količinski popust',
        'start' => null,
        'end' => null
    ],

    'payment' => [
        'providers' => [
            //'wspay'  => \App\Models\Front\Checkout\Payment\Wspay::class,
            //'payway' => \App\Models\Front\Checkout\Payment\Payway::class,
            //'corvus' => \App\Models\Front\Checkout\Payment\Corvus::class,
            'cod'    => \App\Models\Front\Checkout\Payment\Cod::class,
            'bank'   => \App\Models\Front\Checkout\Payment\Bank::class,
            'pickup' => \App\Models\Front\Checkout\Payment\Pickup::class
        ]
    ],

    'sitemap' => [
        0 => 'pages',
        1 => 'categories',
        2 => 'products',
        3 => 'brands',
    ],

];
