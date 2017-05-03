<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model {

    /**
     * Generated
     */

    protected $table = 'pins';
    protected $fillable = ['id', 'comment', 'post_time', 'lat', 'lng', 'user_id'];


    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function tags() {
        return $this->belongsToMany(\App\Models\Tag::class, 'location_tag', 'location_id', 'tag_id');
    }

    public function locationTags() {
        return $this->hasMany(\App\Models\LocationTag::class, 'location_id', 'id');
    }

    public function messages() {
        return $this->hasMany(\App\Models\Message::class, 'location_id', 'id');
    }


}
