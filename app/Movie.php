<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\Filters;
use App\Usefuls\StringTrait;

/**
 * Class Movie
 *
 * @package App
 * @author Rodrigo Cachoeira
 */
class Movie extends Model
{

    use StringTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'api_id', 'title', 'vote_average', 'vote_count', 'popularity',
        'original_language', 'overview', 'release_date', 'external_poster_path',
        'poster_path', 'adult'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'adult' => 'boolean',
        'release_date' => 'date'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'presentableTitle', 'fullImagePath', 'presentableOverview'
    ];

    /**
     * Realiza a consulta dos filmes
     *
     * @param Filters $filter
     * @param int $paginate
     * @return mixed
     */
    public function records(Filters $filter, $paginate = 50)
    {
        return self::filter($filter)->groupBy(['movies.id', 'evaluations.id', 'fits.id'])
            ->with(['genres'])
            ->paginate($paginate, ['movies.*', 'evaluations.liked', 'fits.fit']);
    }

    /**
     * Cria um registro indentificando o status
     * de um filmes relacinado a um usuário, ou seja,
     * se ele gostou ou não do filme
     *
     * @param $like
     * @return bool
     */
    private function handleLike($like)
    {
        try {

            $this->evaluations()
                ->where('user_id', Auth::user()->id)
                ->where('movie_id', $this->id)
                ->delete();

            $this->evaluations()->create([
                'user_id' => Auth::user()->id,
                'movie_id' => $this->id,
                'liked' => $like
            ]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Define que o usuário logado no sistema
     * gostou do filme
     *
     * @return bool
     */
    public function like()
    {
        return $this->handleLike(true);
    }

    /**
     * Define que o usuário logado no sistema
     * não gostou do filme
     *
     * @return bool
     */
    public function dislike()
    {
        return $this->handleLike(false);
    }

    /**
     * Retorna o título do filme
     * de forma limitada para evitar bugs
     * de layout
     *
     * @return string
     */
    public function getPresentableTitleAttribute()
    {
        return $this->stringLimit($this->title, 20);
    }

    /**
     * Retorna a imagem
     *
     * @return string
     */
    public function getFullImagePathAttribute()
    {
        return 'https://image.tmdb.org/t/p/w200/'.$this->external_poster_path;
    }

    /**
     * Retorna a descrição do filme de forma limitada
     *
     * @return string
     */
    public function getPresentableOverviewAttribute()
    {
        return $this->stringLimit($this->overview, 100);
    }

    /**
     * Permite a inclusão dos filtros
     * a entidade
     *
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fits()
    {
        return $this->hasMany(Fit::class);
    }

}