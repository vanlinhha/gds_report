<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $x = [];
        $x['a'] = 'ads';
        $x['b'] = "adss";
        $user = new  \App\User([
       'name' => 'linh',
       'email'  => 'linsh@gmail.com',
       'password' => '11s111111',
       'addition' => json_encode($x)
    ]);
        $user->save();
    }
}
