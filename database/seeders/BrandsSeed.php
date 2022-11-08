<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeed extends Seeder
{

    const BRANDS = [
        'Samsumg', 'HP', 'LG', 'Panasonic',
        'Dell', 'Apple', 'Intel', 'AMD',
        'Huawei', 'Logitech'
    ];
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        for ($i = 0; $i < count(self::BRANDS); $i++) {
            $finding = Brand::where('brand', '=', strtolower(self::BRANDS[$i]))->first();
            if (!is_null($finding)) continue;
            Brand::create([
                'brand' => self::BRANDS[$i]
            ]);
        }
    }
}
