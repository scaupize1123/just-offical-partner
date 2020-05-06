<?php

use Faker\Generator as Faker;

$factory->define(Scaupize1123\JustOfficalPartner\Partner::class, function (Faker $faker) {
    return [
       'uuid' => $faker->uuid(),
       'status' => 1,
    ];
});
