<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OmdbService;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    protected $omdbService;

    public function __construct(OmdbService $omdbService)
    {
        $this->middleware('auth');
        $this->omdbService = $omdbService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->input('search', 'movie');
            $page = $request->input('page', 1);
            $type = $request->input('type');
            $year = $request->input('year');

            $results = $this->omdbService->searchMovies($search, $page, $type, $year);

            // Add favorite status to each movie
            if (isset($results['Search']) && $results['Response'] === 'True') {
                $favoriteIds = Auth::user()
                    ->favoriteMovies()
                    ->pluck('imdb_id')
                    ->toArray();

                foreach ($results['Search'] as &$movie) {
                    $movie['isFavorite'] = in_array($movie['imdbID'], $favoriteIds);
                }
            }

            return response()->json($results);
        }

        return view('movies.index');
    }

    public function show($imdbId)
    {
        $movie = $this->omdbService->getMovieDetail($imdbId);
        
        if (!isset($movie['Response']) || $movie['Response'] === 'False') {
            abort(404, __('messages.movie_not_found'));
        }

        $isFavorite = Auth::user()
            ->favoriteMovies()
            ->where('imdb_id', $imdbId)
            ->exists();

        return view('movies.show', compact('movie', 'isFavorite'));
    }
}