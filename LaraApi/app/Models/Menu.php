<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table='menus';
    protected $primaryKey = 'menu_id';
    protected $fillable = [ 
        'parent_id',
        'menu_title',
        'url',
        'is_sub_menu',
        'sort_order',
        'icon_class',   
        'is_active',
        'added_by',
        'is_migration_data'
    ];

    protected $hidden = [
        'is_migration_data',
    ];
}
