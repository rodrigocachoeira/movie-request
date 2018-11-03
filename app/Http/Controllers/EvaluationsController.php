<?php

namespace App\Http\Controllers;

use App\Movie;

/**
 * Class EvaluationsController
 *
 * @package App\Http\Controllers
 * @author Rodrigo Cachoeira
 * @version 1.0
 * @since 01/11/2018
 */
class EvaluationsController extends Controller
{

    /**
     * EvaluationsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Define que o usuário logado
     * gostou do filme
     *
     * @param Movie $movie
     */
    public function like(Movie $movie)
    {
        if ($movie->like()) {
            return abort(200);
        }
        return abort(503);
    }

    /**
     * Define que o usuário logado
     * não gostou do filme
     *
     * @param Movie $movie
     */
    public function dislike(Movie $movie)
    {
        if ($movie->dislike()) {
            return abort(200);
        }
        return abort(503);
    }

}
