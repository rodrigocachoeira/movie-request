<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GenreRelation
 *
 * @package App
 * @author Rodrigo Cachoeira
 */
class GenreRelation extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'genre_id', 'genre_other_id', 'similarity'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function genreOther()
    {
        return $this->belongsTo(Genre::class, 'genre_other_id');
    }

}
