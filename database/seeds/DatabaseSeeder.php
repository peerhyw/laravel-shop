<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(UserAddressesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CouponCodesTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
    }
}
