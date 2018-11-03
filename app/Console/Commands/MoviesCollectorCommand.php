<?php

namespace App\Console\Commands;

use App\Genre;
use App\Movie;
use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

/**
* Classe que realiza a coleta dos filmes
* em uma api online
* TMDb: https://www.themoviedb.org/documentation/api
*
* @author Rodrigo Cachoeira
* @version 1.0
* @since 30/10/2018
*/
class MoviesCollectorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collector:movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os Filmes com a API';

    /**
     * Método Http de conexão com o protocólo
     * para consultas a api
     *
     * @var Client
     */
    private $http;

    /**
     * Url de requisição para a API
     *
     * @var string
     */
    protected $url = "https://api.themoviedb.org/3/movie/popular?api_key=1362b4142143e708947f435a003523dd&language=en-US&page=";

    /**
     * Colunas que serão importadas para o banco
     *
     * @var array
     */
    protected $field;

    /**
     * Gêneros salvos na base de dados
     * para vinculação
     *
     * @var array
     */
    protected $genres;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->http = new Client();
        $this->genres = Genre::all()->keyBy('api_id');
        $this->fields = collect([
            'title', 'vote_average', 'vote_count', 'popularity',
            'original_language', 'overview', 'release_date',
            'poster_path', 'adult'
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        foreach (range(127, $this->getLastPage()) as $page) {
            foreach ($this->getMovies($page)->results as $movie) {
                if (Movie::where('api_id', $movie->id)->count() == 0) {
                    $this->attachGenres(Movie::create($this->configureData($movie)), $movie->genre_ids);
                }
                $this->info('Movie: ' . $movie->title);
            }
            $this->info('End of Page: '.$page);
        }
    }

    /**
     * Vincula o filme com seus respectivos
     * gêneros
     *
     * @param $movie
     * @param $genres
     */
    private function attachGenres($movie, $genres)
    {
        foreach ($genres as $genre) {
            $movie->genres()->attach($this->genres->get($genre)->id);
        }
    }

    /**
     * Realiza a coleta dos dados do filme
     * e converte para o formato de inserção ao
     * banco de dados da aplicação
     *
     * @param $movie
     * @return \Illuminate\Support\Collection
     */
    private function configureData($movie)
    {
        $data = $this->fields->flip()->map(function ($value, $key) use($movie) {
            return $movie->$key;
        });
        if ($movie->release_date == '') {
            $data->put('release_date', Carbon::now()->format('Y-m-d'));
        }

        $data->put('api_id', $movie->id);
        $data->put('external_poster_path', $movie->poster_path);

        return $data->toArray();
    }

    /**
     * Retorna a quantidade de páginas que
     * existem para requisição dos filmes
     *
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getLastPage()
    {
        return $this->getMovies(1)->total_pages;
    }

    /**
     * Retorna os filmes da API
     *
     * @param $page
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getMovies($page)
    {
        $response = $this->http->request('GET', $this->url.$page);
        $body = $response->getBody();

        return json_decode($body->getContents());
    }
}
