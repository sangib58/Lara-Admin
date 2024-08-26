<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['id'=>1,'title'=>'Ladmin','description'=>'Hello there,Sign in to start your task!','footer'=>'© 2021 Copyright Ladmin']
        ];

        foreach($items as $item)
        {
            Setting::create($item);
        }
    }
}
