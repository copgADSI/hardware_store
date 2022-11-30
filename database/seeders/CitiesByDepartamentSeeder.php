<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Departament;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CitiesByDepartamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $endPoint = Http::get(env('DEPARTAMENTS_OF_COLOMBIA'));
        $endPoint_data = $endPoint->json();
        for ($i = 0; $i < count($endPoint_data); $i++) {
            $departament = Departament::find($endPoint_data[$i]['departamento']);
            if ($departament) continue;
            $current_departament = Departament::create([
                'departament' => $endPoint_data[$i]['departamento']
            ]);
            $cities = $endPoint_data[$i]['ciudades'];
            foreach ($cities as $key => $city) {
                $current_city = City::find($city);
                if ($current_city) continue;
                City::create([
                    'city' => $city,
                    'departament_id' => $current_departament->id
                ]);
            }
        }
    }
}
