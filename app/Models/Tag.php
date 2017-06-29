<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Tag extends Model
{

    /**
     * Generated
     */

    protected $table = 'tags';
    public $timestamps = false;
    protected $fillable = ['id', 'tag_name', 'popularity'];
    protected $casts =
        [
            'id' => 'int',
            'popularity' => 'int',
        ];

    /**
     * @param $request
     * @return mixed
     */
    public function filterByTags($request)
    {
        $tagTable = Tag::getTable();
        return DB::select("SELECT id AS tag_id, tag_name, popularity FROM $tagTable WHERE MATCH(tag_name) AGAINST(? IN BOOLEAN MODE) ORDER BY popularity DESC", ["$request->filter_by_tag*"]);
    }

    public function getTopHashtags()
    {
        return Tag::select(['id AS tag_id', 'tag_name', 'popularity'])
            ->limit(10)
            ->orderBy('popularity', 'DESC')
            ->get();
    }

    public function locations()
    {
        return $this->belongsToMany(\App\Models\Location::class, 'location_tag', 'tag_id', 'location_id');
    }

    public function locationTags()
    {
        return $this->hasMany(\App\Models\LocationTag::class, 'tag_id', 'id');
    }


}
