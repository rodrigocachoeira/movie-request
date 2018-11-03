<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Fit
 *
 * @package App
 */
class Fit extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'movie_id', 'fit'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
