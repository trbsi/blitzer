<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    /**
     * Generated
     */

    protected $table = 'tags';
    public $timestamps = false;
    protected $fillable = ['id', 'tag', 'popularity'];


    public function locations()
    {
        return $this->belongsToMany(\App\Models\Location::class, 'location_tag', 'tag_id', 'location_id');
    }

    public function locationTags()
    {
        return $this->hasMany(\App\Models\LocationTag::class, 'tag_id', 'id');
    }


}
