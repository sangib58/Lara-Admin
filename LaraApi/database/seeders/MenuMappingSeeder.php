<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuMapping;

class MenuMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items=[
            ['menu_mapping_id'=>1,'menu_group_id'=>1,'menu_id'=>2,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_mapping_id'=>2,'menu_group_id'=>1,'menu_id'=>3,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_mapping_id'=>3,'menu_group_id'=>1,'menu_id'=>5,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_mapping_id'=>4,'menu_group_id'=>1,'menu_id'=>6,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_mapping_id'=>5,'menu_group_id'=>1,'menu_id'=>8,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_mapping_id'=>6,'menu_group_id'=>1,'menu_id'=>10,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
            ['menu_mapping_id'=>7,'menu_group_id'=>2,'menu_id'=>10,'is_active'=>true,'added_by'=>1,'is_migration_data'=>true],
        ];

        foreach($items as $item)
        {
            MenuMapping::create($item);
        }
    }
}
