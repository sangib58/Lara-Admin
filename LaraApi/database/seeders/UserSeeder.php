<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['id'=>1,'name'=>'John','email'=>'john@gmail.com','username'=>'admin@2021','password'=>bcrypt('admin@2021'),'user_role_id'=>1,'image_path'=>'http://localhost:8000/\images\1620403252avatar-4.png','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['id'=>2,'name'=>'Helen','email'=>'helen@gmail.com','username'=>'user@2021','password'=>bcrypt('user@2021'),'user_role_id'=>2,'image_path'=>'http://localhost:8000/\images\1620403293avatar-1.png','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
        ];

        foreach($items as $item)
        {
            User::create($item);
        }
    }
}
