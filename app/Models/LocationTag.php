<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationTag extends Model {

    /**
     * Generated
     */

    protected $table = 'location_tag';
    protected $fillable = ['ID', 'tag_id', 'location_id'];


    public function location() {
        return $this->belongsTo(\App\Models\Location::class, 'location_id', 'ID');
    }

    public function tag() {
        return $this->belongsTo(\App\Models\Tag::class, 'tag_id', 'ID');
    }


}
