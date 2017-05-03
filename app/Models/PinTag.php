<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinTag extends Model {

    /**
     * Generated
     */

    protected $table = 'pin_tag';
    protected $fillable = ['id', 'tag_id', 'location_id'];


    public function location() {
        return $this->belongsTo(\App\Models\Location::class, 'location_id', 'id');
    }

    public function tag() {
        return $this->belongsTo(\App\Models\Tag::class, 'tag_id', 'id');
    }


}
