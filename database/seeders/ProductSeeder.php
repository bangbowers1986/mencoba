<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::insert([
            ['name' => 'Kopi', 'price' => 15000, 'stock' => 100],
            ['name' => 'Teh', 'price' => 10000, 'stock' => 50]
        ]);
    }
}