<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pin;
use App\Models\PinTag;
use App\Helpers\PinHelper;
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
     * @param $tag string Some hashtag
     * @return mixed
     */
    public function filterByTags($tag, $current_time, $authUser, $lat, $lng) 
    {
        $pinIds = (new Pin)->getPinBasicQuery($current_time, $authUser, $lat, $lng)->get()->pluck('id');
        $pinTagTable = (new PinTag)->getTable();
        $tagTable = Tag::getTable();
        
        return DB::select("
            SELECT id AS tag_id, tag_name, 
            (SELECT COUNT(tag_id) FROM $pinTagTable WHERE tag_id = $tagTable.id AND pin_id IN (".implode(",", $pinIds->toArray()).")) AS popularity 
            FROM $tagTable 
            WHERE MATCH(tag_name) AGAINST(? IN BOOLEAN MODE) 
            ORDER BY popularity DESC", ["$tag*"]);
    }

    /**
     * Get top hashtags for that specific area where user is
     * @param  string $current_time Current time on phone
     * @param  object $authUser Authenticated user
     * @param  float $lat Latitude
     * @param  float $lng Longitude
     * @return Collection
     */
    public function getTopHashtags($current_time, $authUser, $lat, $lng)
    {
        //little help - https://stackoverflow.com/questions/44003969/how-to-get-a-belongstomany-query-from-a-collection-mysql-laravel
        $pinIds = (new Pin)->getPinBasicQuery($current_time, $authUser, $lat, $lng)->get()->pluck('id');
        $pinTagTable = (new PinTag)->getTable();
        $tagTable = Tag::getTable();

        return Tag::select(['id AS tag_id', 'tag_name'])
            ->selectRaw("(SELECT COUNT(tag_id) FROM $pinTagTable WHERE tag_id = $tagTable.id AND pin_id IN (".implode(",", $pinIds->toArray()).")) AS popularity")
            ->limit(10)
            ->orderBy('popularity', 'DESC')
            ->groupBy('id')
            ->whereHas('pins', function($query) use ($pinIds) {
                $query->whereIn('pin_id', $pinIds); //pin_id from pin_tag table
            })
            ->get();
    }

    public function pins()
    {
        return $this->belongsToMany(\App\Models\Pin::class, 'pin_tag', 'tag_id', 'pin_id');
    }

    public function locationTags()
    {
        return $this->hasMany(\App\Models\LocationTag::class, 'tag_id', 'id');
    }


}
