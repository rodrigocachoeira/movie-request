<?php

namespace App\Http\Controllers;

use App\Http\Filters\MoviesFilter;
use App\Movie;

/**
 * Class MoviesController
 *
 * @package App\Http\Controllers
 * @author Rodrigo Cachoeira
 */
class MoviesController extends Controller
{

    /**
     * @var Movie
     */
    private $movie;

    /**
     * Create a new controller instance.
     *
     * @param Movie $movie
     */
    public function __construct(Movie $movie)
    {
        $this->middleware(['auth', 'loader.genres']);

        $this->movie = $movie;
    }

    /**
     * Show movies list of application.
     *
     * @param MoviesFilter $moviesFilter
     * @return \Illuminate\Http\Response
     */
    public function index(MoviesFilter $moviesFilter)
    {
        $movies = $this->movie->records($moviesFilter, 50);
        return view('movies.index', compact('movies'));
    }

}
