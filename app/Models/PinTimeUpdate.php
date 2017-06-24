<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinTimeUpdate extends Model
{

    /**
     * Generated
     */

    protected $table = 'pin_time_update';
    public $timestamps = false;
    protected $fillable = ['id', 'user_id'];
    protected $casts =
        [
            'id' => 'int',
            'user_id' => 'int',
        ];

}
