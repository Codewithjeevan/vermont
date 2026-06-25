<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
	static $password;

	return [
	'name' => $faker->name,
	'email' => $faker->unique()->safeEmail,
	'password' => $password ?: $password = bcrypt('secret'),
	'remember_token' => str_random(10),
	];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {

	return [
	'categorie' => $faker->word,
	'parent_category' => random_int(\DB::table('categories')->where("parent_category","=","0")->min('id'), \DB::table('categories')->where("parent_category","=","0")->max('id')),
	];
});

$factory->define(App\Item::class, function (Faker\Generator $faker) {
	return [
	'productCode'=> $faker->bothify("??#########"),
	'category'=> random_int(\DB::table('categories')->where("parent_category","<>","0")->min('id'), \DB::table('categories')->where("parent_category","<>","0")->max('id')),
	'barcode'=> $faker->isbn13,
	'itemName'=> $faker->word,
	'itemNameArabic'=>'يتحدث',
	'costPrise'=> $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 100),
	'salePrise'=> $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 100),
	'description'=> $faker->sentence,
	'unit'=> random_int(\DB::table('units')->min('id'), \DB::table('units')->max('id')),
	'stock'=> 0,
	'min'=> $faker->numberBetween(0, 10),
	'max'=> $faker->numberBetween(15,100),
	'expiry_date'=> $faker->date($format = 'Y-m-d', $max = '+30 months'),
	'image'=> $faker->imageUrl(150, 150 , 'food'),
	];
});


$factory->define(App\Customer::class, function (Faker\Generator $faker) {
	return [
	"title"=> $faker->numberBetween(1,3),
	"name"=> $faker->firstNameMale ." ". $faker->lastName,
	"spouse"=> $faker->firstNameFemale ." ". $faker->lastName,
	"birthday"=> $faker->date($format = 'Y-m-d', $max = '-20 years'),
	"anniversary"=> $faker->date($format = 'Y-m-d', $max = '-10 years'),
	"address"=> $faker->streetAddress,
	"phone"=> $faker->e164PhoneNumber,
	"mobile"=> $faker->phoneNumber,
	"fax"=> $faker->e164PhoneNumber,
	"email"=> $faker->email,
	"city"=> $faker->city,
	];
});


$factory->define(App\Supplier::class, function (Faker\Generator $faker) {
	return [
	"type"=> $faker->randomElement(["Supplier","Branch","Dealer","Godown","Manufacturer", "Retail"]),
	"title"=> $faker->numberBetween(1,3),
	"name" => $faker->firstNameMale,
	"contact"=> $faker->firstNameMale,
	"address"=> $faker->streetAddress,
	"phone"=> $faker->e164PhoneNumber,
	"mobile"=> $faker->phoneNumber,
	"fax"=> $faker->e164PhoneNumber,
	"email"=> $faker->email,
	"city"=> $faker->city,
	"ecc"=> $faker->word,
	"cst"=> $faker->word,
	"range"=> $faker->word,
	"division"=> $faker->word,
	"comm"=> $faker->word,
	"pan"=> $faker->word,
	"tin"=> $faker->word,
	];
});

// Salesman
$factory->define(App\Salesman::class, function (Faker\Generator $faker) {
	return [
	"name"=> $faker->firstNameMale." ".$faker->lastName,
	"desigination"=> 0,
	];
});

// opening stock
$factory->define(App\OpeningStock::class, function (Faker\Generator $faker) {
	return [
	"open_stock_date" => $faker->date($format = 'Y-m-d', $max = 'now'),
	"itemId" => random_int(\DB::table('items')->min('id'), \DB::table('items')->max('id')),
	"openingStock" => $faker->numberBetween(100,3000),
	"unit" => random_int(\DB::table('units')->min('id'), \DB::table('units')->max('id')),
	];
});

//Bank
$factory->define(App\Bank::class, function (Faker\Generator $faker) {
	return [
	"name" => $faker->state. " bank of ". $faker->country
	];
});
//Location

