<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['menu_id'=>1,'parent_id'=>0,'menu_title'=>'Menu','url'=>'','is_sub_menu'=>1,'sort_order'=>1,'icon_class'=>'menu_open','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>2,'parent_id'=>1,'menu_title'=>'Menu List','url'=>'/menu/menuList','is_sub_menu'=>0,'sort_order'=>0,'icon_class'=>'','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>3,'parent_id'=>1,'menu_title'=>'Menu Group List','url'=>'/menu/menuGroupList','is_sub_menu'=>0,'sort_order'=>0,'icon_class'=>'','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>4,'parent_id'=>0,'menu_title'=>'User','url'=>'','is_sub_menu'=>1,'sort_order'=>2,'icon_class'=>'mdi-account-multiple','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>5,'parent_id'=>4,'menu_title'=>'User List','url'=>'/user/userList','is_sub_menu'=>0,'sort_order'=>0,'icon_class'=>'','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>6,'parent_id'=>4,'menu_title'=>'Role List','url'=>'/user/userRoleList','is_sub_menu'=>0,'sort_order'=>0,'icon_class'=>'','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>7,'parent_id'=>0,'menu_title'=>'History','url'=>'','is_sub_menu'=>1,'sort_order'=>3,'icon_class'=>'history','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>8,'parent_id'=>7,'menu_title'=>'Browse History','url'=>'/user/browseList','is_sub_menu'=>0,'sort_order'=>0,'icon_class'=>'','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>9,'parent_id'=>0,'menu_title'=>'Settings','url'=>'','is_sub_menu'=>1,'sort_order'=>4,'icon_class'=>'settings','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_id'=>10,'parent_id'=>9,'menu_title'=>'App Settings','url'=>'/settings/appSettings','is_sub_menu'=>0,'sort_order'=>0,'icon_class'=>'','is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
        ];

        foreach($items as $item)
        {
            Menu::create($item);
        }
    }
}
