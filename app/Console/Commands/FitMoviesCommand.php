<?php

namespace App\Console\Commands;

use App\Analyzes\RBC;
use App\Movie;
use App\User;
use Illuminate\Console\Command;

/**
 * Class FitMoviesCommand
 * Realiza o calculo de porcentagem utilizando
 * algoritmo baseado em RBC para verificar a taxa de
 * similaridade dos filmes com os outros filmes assistidos
 * e marcados como gostei ou não gostei pelos usuários
 *
 * @package App\Console\Commands
 */
class FitMoviesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:fit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza o Treinamento do algoritmo RBC';

    /**
     * @var RBC
     */
    protected $rbc;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        set_time_limit(0);
        $this->rbc = new RBC();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $movies = Movie::with(['evaluations', 'genres'])->get();
        $users = User::all();

        $bar = $this->output->createProgressBar($movies->count() * $users->count());

        foreach($users as $user) {
            $likedMovies = $user->evaluations()->with('movie')->where('liked', true)->get();
            //$dislikedMovies = $user->evaluations()->where('liked', false)->get();

            foreach ($movies as $movie) {
                $this->rbc->setUser($user)->prepare($movie, $likedMovies)->analyze();
                $bar->advance();
            }
        }
        $bar->finish();
    }
}
