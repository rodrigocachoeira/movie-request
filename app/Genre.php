<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Genre
 *
 * @author Rodrigo Cachoeira
 * @package App
 */
class Genre extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'api_id'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'alias'
    ];

    /**
     * Converte o nome para um formato
     * mais amigável
     *
     * @param $alias
     * @return string
     */
    public function getAliasAttribute($alias = null)
    {
        if (is_null($alias)) {
            return str_replace(' ', '_', strtolower($this->name));
        }
        return ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function movie()
    {
        return $this->belongsToMany(Movie::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leftRelations()
    {
        return $this->hasMany(GenreRelation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rightRelations()
    {
        return $this->hasMany(GenreRelation::class, 'genre_other_id');
    }

    /**
     * @return Collection
     */
    public function relations()
    {
        $invert = $this->rightRelations->map(function ($genre) {
            $temp = $genre->genre_id;
            $genre->genre_id = $genre->genre_other_id;
            $genre->genre_other_id = $temp;

            return $genre;
        });

        return $this->leftRelations->merge($invert)->unique('id');
    }

}
