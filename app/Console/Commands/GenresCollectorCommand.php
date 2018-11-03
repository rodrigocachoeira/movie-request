<?php

namespace App\Console\Commands;

use App\Genre;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

/**
 * Classe que realiza a coleta dos gêneros
 * em uma api online
 * TMDb: https://www.themoviedb.org/documentation/api
 *
 * @author Rodrigo Cachoeira
 * @version 1.0
 * @since 30/10/2018
 */
class GenresCollectorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collector:genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os Gêneros com a API';

    /**
     * Objeto de Conexão com o protocólo Http
     *
     * @var Client
     */
    private $http;

    /**
     * Link de consulta a API
     *
     * @var String
     */
    private $url = "https://api.themoviedb.org/3/genre/movie/list?api_key=1362b4142143e708947f435a003523dd&language=en-US";

    /**
     * Campos que serão importados
     *
     * @var array
     */
    private $fields = ['api_id', 'title', 'vote_average', 'vote_count', 'popularity',
        'original_language', 'overview', 'release_date', 'external_poster_path',
        'poster_path', 'adult'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->http = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        foreach($this->getGenres()->genres as $genre) {
            if (Genre::where('api_id', $genre->id)->count() == 0) {
                Genre::create([
                   'api_id' => $genre->id,
                   'name' => $genre->name
                ]);
            }
        }
    }

    /**
     * Retorna os gêneros da API
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getGenres()
    {
        $response = $this->http->request('GET', $this->url);
        $body = $response->getBody();

        return json_decode($body->getContents());
    }
}
