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
        $controller = null;

    }
}
