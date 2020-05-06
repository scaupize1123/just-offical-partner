<?php

use Faker\Generator as Faker;

$factory->define(Scaupize1123\JustOfficalPartner\PartnerTranslation::class, function (Faker $faker) {
    return [
       'name' => $faker->text($maxNbChars = 100),
       'brief' => $faker->text($maxNbChars = 200),
       'email' => $faker->text($maxNbChars = 100),
       'phone' => $faker->text($maxNbChars = 50),
       'link' => $faker->text($maxNbChars = 200),
       'language_id' => 1,
       'partner_id' => 1,
       'status' => 1,
    ];
});
