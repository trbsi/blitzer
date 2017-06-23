<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinTag extends Model
{

    /**
     * Generated
     */

    protected $table = 'pin_tag';
    public $timestamps = false;
    protected $fillable = ['id', 'tag_id', 'pin_id'];
    protected $casts = 
    [
        'id' => 'int',
        'tag_id' => 'int',
        'pin_id' => 'int',
    ];


    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id', 'id');
    }

    public function relationTag()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'tag_id', 'id');
    }


}
