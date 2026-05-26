<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCities extends Command
{
    protected $signature = 'import:cities';

    protected $description = 'İl ve ilçeleri içeri aktar';

    public function handle()
    {
        $path = storage_path('app/cities.json');

        if (!file_exists($path)) {
            $this->error('cities.json bulunamadı!');
            return;
        }

        $json = file_get_contents($path);
        $cities = json_decode($json, true);

        foreach ($cities as $cityData) {

            $city = City::firstOrCreate(
                [
                    'name' => $cityData['name'],
                    'parent_id' => null,
                ],
                [
                    'slug' => Str::slug($cityData['name']),
                    'is_active' => true,
                ]
            );

            if (!empty($cityData['districts'])) {

                foreach ($cityData['districts'] as $district) {

                    City::firstOrCreate(
                        [
                            'name' => $district,
                            'parent_id' => $city->id,
                        ],
                        [
                            'slug' => Str::slug($cityData['name'] . '-' . $district),
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        $this->info('İller ve ilçeler başarıyla eklendi.');
    }
}