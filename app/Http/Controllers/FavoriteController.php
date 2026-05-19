<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavoriteMovie;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $favorites = Auth::user()
            ->favoriteMovies()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'imdb_id' => 'required|string',
            'title' => 'required|string',
            'year' => 'required|string',
            'poster' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        try {
            $favorite = Auth::user()->favoriteMovies()->updateOrCreate(
                ['imdb_id' => $request->imdb_id],
                [
                    'title' => $request->title,
                    'year' => $request->year,
                    'poster' => $request->poster,
                    'type' => $request->type,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => __('messages.favorite_added'),
                'favorite' => $favorite
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred')
            ], 500);
        }
    }

    public function destroy($imdbId)
    {
        try {
            $deleted = Auth::user()
                ->favoriteMovies()
                ->where('imdb_id', $imdbId)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.favorite_removed')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('messages.favorite_not_found')
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred')
            ], 500);
        }
    }
}