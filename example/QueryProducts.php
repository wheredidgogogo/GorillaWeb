<?php

use Gorilla\Client;

require __DIR__.'/../vendor/autoload.php';

$id = '__PUT_YOUR_WEBSITE_ID__';
$token = '__PUT_YOUR_WEBSITE_ACCESS_TOKEN';

$client = new Client($id, $token);

$data = $client->query('products')
    ->fields([
        'id',
        'name',
        'slug',
        'menu_label',
        'status',
        'heading',
        'sub_heading',
        'caption',
        'description',
        'page_heading',
        'page_sub_heading',
        'path',
        'url',
    ])
    ->get()
    ->json();

var_dump($data);