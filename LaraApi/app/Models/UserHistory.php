<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    protected $table='log_histories';
    protected $primaryKey = 'log_history_id';
    public $timestamps = false;
    protected $fillable=[
        'log_code',
        'user_id',
        'log_date',
        'log_in_time',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'user_ip',
    ];
}
