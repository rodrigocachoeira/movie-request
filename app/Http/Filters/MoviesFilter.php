<?php

namespace App\Http\Filters;

use App\Http\Middleware\GenresLoader;

/**
 * Class MoviesFilter
 *
 * @package App\Http\Filters
 * @author Rodrigo Cachoeira
 */
class MoviesFilter extends Filters
{

    /**
     * @var array
     */
    protected $filters = [
        'genre', 'marked', 'order', 'title'
    ];

    /**
     * @var array
     */
    protected $fixedFilters = [
        'genres', 'evaluations', 'fits', 'popularity'
    ];

    /**
     * Ordena os filmes por ordem de lançamento
     */
    protected function popularity()
    {
        $this->builder->orderBy('popularity', 'DESC');
    }

    /**
     * Realiza o vinculo dos filmes com
     * suas respectivas avaliações feitas
     * pelo usuário logado no sistema
     */
    protected function evaluations()
    {
        $this->builder->leftJoin('evaluations', function($join) {
            $join->on('movies.id', '=', 'evaluations.movie_id')
                ->where('evaluations.user_id', auth()->user()->id);
        });
    }

    /**
     * Realiza o vinculo dos filmes com
     * suas respectivas notas feitas
     * calculadas pelo algoritmo RBC
     */
    protected function fits()
    {
        $this->builder->leftJoin('fits', function($join) {
            $join->on('movies.id', '=', 'fits.movie_id')
                ->where('fits.user_id', auth()->user()->id);
        });
    }

    /**
     * Realiza o vinculo com os gêneros
     */
    protected function genres()
    {
        $this->builder
            ->join('genre_movie', 'movies.id', '=', 'genre_movie.movie_id');
    }

    /**
     * Realiza o filtro pelo filme
     * de acordo com o gênero passado
     * como parâmetro
     *
     * @param $value
     * @throws \Exception
     */
    public function genre($value)
    {
        if ($value) {
            $genre = GenresLoader::getGenres()->keyBy('name')->get($value);

            if (! is_null($genre)) {
                $this->builder->where('genre_movie.genre_id', $genre->id);
            }
        }
    }

    /**
     * Apresenta apenas os filmes que
     * possuem a marcação de liked ou
     * disliked
     *
     * @param $value
     */
    public function marked($value)
    {
        if ($value) {
            $this->builder->where('evaluations.liked', $value == 'liked');
        }
    }

    /**
     * Aplica uma ordenação ao
     * conjunto de filmes
     *
     * @param $value
     */
    public function order($value)
    {
        if ($value) {
            $this->builder->orderBy('fits.fit', 'DESC');
            if ($value == 'recommendation') {
                $this->builder->whereNull('evaluations.liked');
            }
        }
    }

}