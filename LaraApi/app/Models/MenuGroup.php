<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    protected $table='menu_groups';
    protected $primaryKey = 'menu_group_id'; 
    protected $fillable = [ 
        'menu_group_name',   
        'is_active',
        'added_by',
        'is_migration_data'
    ];

    protected $hidden = [
        'is_migration_data',
    ];
}
