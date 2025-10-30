<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic 2.4GHz wireless mouse with USB receiver.',
                'price' => 39.90,
                'image' => 'https://images.unsplash.com/photo-1707592691247-5c3a1c7ba0e3?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8d2lyZWxlc3MlMjBtb3VzZXxlbnwwfHwwfHx8MA%3D%3D&fm=jpg&q=60&w=3000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB backlit mechanical keyboard with blue switches.',
                'price' => 159.00,
                'image' => 'https://img.freepik.com/premium-photo/minimalist-mechanical-keyboard-with-white-background_977291-5.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Noise Cancelling Headphones',
                'description' => 'Over-ear wireless headphones with active noise cancellation.',
                'price' => 299.00,
                'image' => 'https://hnsgsfp.imgix.net/9/images/detailed/91/sony-wh-ch720n-wireless-noise-cancelling-headphone-white_1.jpg?fit=fill&bg=0FFF&w=1534&h=900&auto=format,compress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub with HDMI, SD card, and Ethernet ports.',
                'price' => 89.90,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3ugO037TnzlYmmrGrpGab6W4fyZI5YxGhiw&s',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
