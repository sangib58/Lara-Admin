<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuGroup;

class MenuGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['menu_group_id'=>1,'menu_group_name'=>'Super Admin Group','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_group_id'=>2,'menu_group_name'=>'User Group','is_active'=>true,'added_by'=>1,'is_migration_data'=>true]
        ];

        foreach($items as $item)
        {
            MenuGroup::create($item);
        }
    }
}
