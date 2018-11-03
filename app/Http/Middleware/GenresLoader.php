<?php

namespace App\Http\Middleware;

use App\Genre;
use Closure;

/**
 * Class GenresLoader
 *
 * @package App\Http\Middleware
 * @author Rodrigo Cachoeira
 * @version 1.0
 * @since 01/11/2018
 */
class GenresLoader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        $genres = self::getGenres();
        view()->composer('*', function ($view) use ($genres) {
            $view->with([
                'genres' => $genres
            ]);
        });

        return $next($request);
    }


    /**
     * Retorna os gêneros de forma
     * cacheada
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getGenres()
    {
        return cache()->rememberForever('genres', function () {
            return Genre::orderBy('name', 'ASC')->get();
        });
    }
}
