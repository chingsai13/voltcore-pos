<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users (Admin, Staff, Customer)
        DB::table('users')->insert([
            [
                'username'   => 'admin',
                'full_name'  => 'Store Admin',
                'email'      => 'admin@voltcore.com',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'avatar'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'staff01',
                'full_name'  => 'Staff One',
                'email'      => 'staff@voltcore.com',
                'password'   => Hash::make('password'),
                'role'       => 'staff',
                'avatar'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'customer01',
                'full_name'  => 'Mika Santos',
                'email'      => 'customer@voltcore.com',
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'avatar'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Customers
        DB::table('customers')->insert([
            [
                'full_name'  => 'Mika Santos',
                'email'      => 'mika@mail.com',
                'phone'      => '0917 555 0101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Joaquin Reyes',
                'email'      => 'jq@mail.com',
                'phone'      => '0918 555 0142',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Lia Fernandez',
                'email'      => 'lia@mail.com',
                'phone'      => '0919 555 0199',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Products
        DB::table('products')->insert([
            ['name' => 'Ryzen 7 9700X Processor', 'category' => 'CPU', 'price' => 21995.00, 'stock_quantity' => 14, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Core Ultra 7 265K Processor', 'category' => 'CPU', 'price' => 23450.00, 'stock_quantity' => 3, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'RTX 5070 12GB Graphics Card', 'category' => 'GPU', 'price' => 38990.00, 'stock_quantity' => 6, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'RX 9060 XT 16GB Graphics Card', 'category' => 'GPU', 'price' => 25500.00, 'stock_quantity' => 2, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '32GB DDR5-6000 Memory Kit', 'category' => 'Memory', 'price' => 6990.00, 'stock_quantity' => 22, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '2TB NVMe Gen4 SSD', 'category' => 'Storage', 'price' => 7250.00, 'stock_quantity' => 17, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '27-inch 1440p 165Hz Monitor', 'category' => 'Display', 'price' => 14800.00, 'stock_quantity' => 8, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '75% Mechanical Keyboard', 'category' => 'Peripherals', 'price' => 3890.00, 'stock_quantity' => 5, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Initial Sales Transactions
        DB::table('sales')->insert([
            [
                'product_id'     => 3,
                'customer_id'    => 2,
                'sold_by'        => 2, // staff01
                'quantity'       => 1,
                'total_price'    => 38990.00,
                'payment_method' => 'Cash',
                'created_at'     => now()->subDays(2),
                'updated_at'     => now()->subDays(2),
            ],
            [
                'product_id'     => 5,
                'customer_id'    => null,
                'sold_by'        => 2,
                'quantity'       => 2,
                'total_price'    => 13980.00,
                'payment_method' => 'GCash',
                'created_at'     => now()->subDay(),
                'updated_at'     => now()->subDay(),
            ],
            [
                'product_id'     => 7,
                'customer_id'    => 1,
                'sold_by'        => 1, // admin
                'quantity'       => 1,
                'total_price'    => 14800.00,
                'payment_method' => 'Bank Transfer',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}
