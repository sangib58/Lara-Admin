<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MenuMapping extends Model
{
    protected $table='menu_mappings';
    protected $primaryKey = 'menu_mapping_id'; 
    protected $fillable = [
        'menu_group_id',    
        'menu_id',   
        'is_active',
        'added_by',
        'is_migration_data'
    ];

    protected $hidden = [
        'is_migration_data',
    ];
    
}
