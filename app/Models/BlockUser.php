<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{

    /**
     * Generated
     */

    protected $table = 'block_user';
    protected $fillable = ['id', 'who_is_blocked', 'blocked_by'];


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'blocked_by', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'who_is_blocked', 'id');
    }


}
