<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mobil>
 */
class MobilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $json = File::get("database/data/cars.json");
        $cars = json_decode($json);
        $randomNumber = rand(0, count($cars));
        $randomCars = $cars[$randomNumber];
        $randomWord = Str::upper(Str::random(3));
        $plat = 'BE '. rand(1111,9999).' '. $randomWord;
        return [
            'merek' => explode(' ', $randomCars->model)[0],
            'model' => $randomCars->model,
            'nomor_plat' => $plat,
            'tarif' => rand(100000,1000000),
            'user_id' => rand(1,10)
        ];
    }
}
