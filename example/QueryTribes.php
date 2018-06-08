<?php

use Gorilla\Client;

require __DIR__.'/../vendor/autoload.php';

$id = '__PUT_YOUR_WEBSITE_ID__';
$token = '__PUT_YOUR_WEBSITE_ACCESS_TOKEN';

$client = new Client($id, $token);

$data = $client->query('tribes')
    ->filters([
        // change to your tribe type name
        'name' => 'Signarama France Magasin',
    ])
    ->fields([
        'name',
        'country',
        'postal_code',
        'state',
        'locality',
        'address_2',
        'address_1',
        'latitude',
        'longitude',
        'slug',
        'public_email',
        'main_telephone',
        'heading',
        'sub_heading',
        'caption',
        'page_heading',
        'page_sub_heading',
        'introduction_bold',
        'introduction',
        'tracking_code',
        'facebook_pixel_id',
        'answer_number',
        'organic_number',
        'paid_number',
        'introduction_team',
        'opening_hours',
        'opening_hours_message',
        'show_opening_hours',
    ])
    ->get()
    ->json();

var_dump($data);