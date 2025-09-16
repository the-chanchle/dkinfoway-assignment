<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Store;

class StoresTableSeeder extends Seeder
{
    public function run()
    {
        $stores = [
            ['name' => 'Central Warehouse', 'code' => 'CW'],
            ['name' => 'North Branch', 'code' => 'NB'],
            ['name' => 'South Branch', 'code' => 'SB'],
        ];

        foreach ($stores as $s) {
            Store::create($s);
        }
    }
}