<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Sate Ayam', 'price' => 30000, 'image' => '/images/sate-ayam.jpg'],
            ['name' => 'Bakso', 'price' => 10000, 'image' => '/images/bakso.jpg'],
            ['name' => 'Tempe Goreng', 'price' => 5000, 'image' => '/images/tempe.jpg'],
            ['name' => 'Tahu Isi', 'price' => 10000, 'image' => '/images/tahu-isi.jpg'],
            ['name' => 'Soto Ayam', 'price' => 30000, 'image' => '/images/soto-ayam.jpg'],
            ['name' => 'Nasi Padang', 'price' => 30000, 'image' => '/images/nasi-padang.jpg'],
            ['name' => 'Taco', 'price' => 30000, 'image' => '/images/taco.jpg'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

    }
}
