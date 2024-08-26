<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['user_role_id'=>1,'role_name'=>'Admin','menu_group_id'=>1,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['user_role_id'=>2,'role_name'=>'User','menu_group_id'=>2,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true]
        ];

        foreach($items as $item)
        {
            UserRole::create($item);
        }
    }
}
