<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Evaluation
 *
 * @package App
 * @author Rodrigo Cachoeira
 * @version 1.0
 * @since 01/11/2018
 */
class Evaluation extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'movie_id', 'liked'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'liked' => 'boolean'
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
