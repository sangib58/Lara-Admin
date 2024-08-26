<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table='user_roles';
    protected $primaryKey = 'user_role_id';
    protected $fillable = [
        'role_name',    
        'menu_group_id',   
        'role_desc',
        'added_by',
        //'is_migration_data'
    ];

    protected $hidden = [
        'is_migration_data',
    ];
}
