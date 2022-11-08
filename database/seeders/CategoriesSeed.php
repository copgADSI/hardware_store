<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeed extends Seeder
{
    const CATEGORIES = [
        'USB', 'PC Gamer', 'PC Oficina',
        'Portafil', 'Diademas', 'Mouses'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < count(self::CATEGORIES); $i++) {
            $finding = Category::where('category', '=', strtolower(self::CATEGORIES[$i]))->first();
            if (!is_null($finding)) continue;
            Category::create([
                'category' => self::CATEGORIES[$i]
            ]);
        }
    }
}
