<?php

namespace App\Analyzes;

use App\Fit;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use App\Genre;
use App\Movie;

/**
 * Class RBC
 * Classe que implementa as verificações
 * de similaridade utilizando o algoritmo RBC
 *
 * @package App\Analyzes
 * @version 1.0
 * @author Rodrigo Cachoeira
 */
class RBC
{

    /**
     * @var Movie
     */
    private $movies;

    /**
     * @var Movie
     */
    private $movie;

    /**
     * @var Collection
     */
    private $genreRelations;

    /**
     * @var array
     */
    private $weights;

    /**
     * @var User
     */
    private $user;

    /**
     * RBC constructor.
     */
    public function __construct()
    {
        $this->genreRelations = Genre::all()->keyBy('id');
        $this->weights = [
            'genres' => 5,
            'title' => 1,
            'overview' => 2,
            'release_date' => 2,
            'note' => 4
        ];
    }

    /**
     * Define o usuário
     *
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Prepara a classe para análise
     *
     * @param $movie
     * @param $movies
     * @return $this
     */
    public function prepare($movie, $movies)
    {
        $this->movies = $movies;
        $this->movie = $movie;

        return $this;
    }

    /**
     * Realiza a implementação do raciocionio
     * baseado e casos para os conjuntos
     * especificados
     *
     * @return double valor calculado
     */
    public function analyze()
    {
        $notes = collect();
        foreach ($this->movies as $evaluation) {
            $attributes = $this->applyWeights($this->calcPercent($this->movie, $evaluation->movie));

            $notes->push(collect($attributes)->sum() / collect($this->weights)->sum());
        }

        $this->storeFit($notes);
    }

    /**
     * Com base nas notas calculadas
     * armazena a média delas ao filmes
     * e ao usuário
     *
     * @param $notes
     */
    private function storeFit($notes)
    {
        Fit::create([
            'movie_id' => $this->movie->id,
            'user_id' => $this->user->id,
            'fit' => $notes->median()
        ]);
    }

    /**
     * Aplica os pesos as notas
     * calculdadas pelo algoritmo
     *
     * @param $notes
     * @return array
     */
    private function applyWeights($notes)
    {
        foreach ($notes as $key => $value) {
            $notes[$key] = $notes[$key] * $this->weights[$key];
        }
        return $notes;
    }

    /**
     * Realiza o calculo de porcentagem de
     * cada atributo definido
     *
     * @param $self
     * @param $other
     * @return array
     */
    private function calcPercent($self, $other)
    {
        similar_text($self->overview, $other->overview, $overview);
        similar_text($self->title, $other->title, $title);

        return [
            'genres' => $this->calcGenres($self->genres, $other->genres),
            'title' => $title / 100,
            'overview' => $overview / 100,
            'release_date' => $this->calcReleaseDate($self->release_date, $other->release_date),
            'note' => $other->vote_average / 10
        ];
    }

    /**
     * Retorna a porcentagem de similaridade entre
     * duas datas
     *
     * @param $self
     * @param $other
     * @return float|int
     */
    private function calcReleaseDate($self, $other)
    {
        $percent = 1 - ($self->diff($other)->y * 0.02);
        return $percent < 0 ? 0 : $percent;
    }

    /**
     * Realiza o calculo de similaridade dos gêneros
     * do filme A com os gêneros do filme B, e retorna
     * a média da similaridade dos gêneros
     *
     * @param $self
     * @param $other
     * @return mixed
     */
    private function calcGenres($self, $other)
    {
        $relations = collect();
        foreach ($self as $selfGenre) {
            foreach ($other as $otherGenre) {

                $relation = $this->genreRelations->get($selfGenre->id)
                    ->relations()
                    ->keyBy('genre_other_id')
                    ->get($otherGenre->id);

                $relations->push($relation);
            }
        }

        return $relations->median('similarity');
    }

}