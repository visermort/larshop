<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $controller = new Controller;
        $controller -> saveConfig('adminEmail','admin@loftschol.com');
        $controller -> saveConfig('siteName','Интренет-магазин "Всё за 1010 руб."');
        $controller -> saveConfig('itemsOnPage','12');

        $controller = null;

    }
}
