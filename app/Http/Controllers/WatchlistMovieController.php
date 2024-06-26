<?php

namespace App\Http\Controllers;

use App\Models\WatchlistMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WatchlistMovieController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $watchlistMovies = WatchlistMovie::with('movies')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return Inertia::render('WatchlistMovies/Index', [
            'watchlistMovies' => $watchlistMovies[0],
        ]);
    }

    public function getPending()
    {
        $user = auth()->user();

        $watchlist = $user->watchlistMovies()->first(); // Get the user's watchlist

        if ($watchlist) {
            $pendingMovies = $watchlist->movies()->wherePivot('watch_status', 'pending')->get();
        } else {
            $pendingMovies = []; // User doesn't have a watchlist yet
        }

        return response()->json($pendingMovies);
    }

    public function getCompleted()
    {
        $user = auth()->user();
        $watchlist = $user->watchlistMovies()->first();

        if ($watchlist) {
            // Cargar las películas completadas con las críticas asociadas del usuario autenticado
            $completedMovies = $watchlist->movies()
                ->wherePivot('watch_status', 'completed')
                ->with(['reviews' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->latest()
                ->get();
        } else {
            $completedMovies = [];
        }

        $userId = $user->id;

        return response()->json([
            'userId' => $userId,
            'completedMovies' => $completedMovies,
        ]);
    }

    public function checkWatchStatus($movieId)
    {
        $user = auth()->user();

        $pivotRecord = WatchlistMovie::where('user_id', $user->id)
            ->first()
            ->movies()
            ->where('movie_id', $movieId)
            ->first();

        if (!$pivotRecord) {
            return response()->json(['watch_status' => null]); // Si la película no está en la lista
        }

        return response()->json(['watch_status' => $pivotRecord->pivot->watch_status]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'movie_id' => 'required|exists:movies,id'
        ]);

        $user = $request->user();
        $watchlist = WatchlistMovie::where('user_id', $user->id)->firstOrFail();

        if (!$watchlist->movies()->where('movie_id', $validatedData['movie_id'])->exists()) {
            $watchlist->movies()->attach($validatedData['movie_id'], ['watch_status' => 'pending']);
        }

        return redirect()->back()->with('message', 'Movie added to watchlist');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        $validatedData = $request->validate([
            'movie_id' => 'required|integer',
            'watch_status' => 'required|string|in:pending,completed'
        ]);

        $user = auth()->user();

        $pivotRecord = WatchlistMovie::where('user_id', $user->id)
            ->first()
            ->movies()
            ->where('movie_id', $validatedData['movie_id'])
            ->first();

        if (!$pivotRecord) {
            return response()->json([
                'error' => 'Movie not found in your watchlist'
            ], 404);
        }

        $pivotRecord->pivot->watch_status = $validatedData['watch_status'];
        $pivotRecord->pivot->save();


        return response()->json(['message' => 'Watch status updated successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $movieId)
    {
        $user = $request->user();
        $watchlist = WatchlistMovie::where('user_id', $user->id)->firstOrFail();

        if ($watchlist->movies()->where('movie_id', $movieId)->exists()) {
            $watchlist->movies()->detach($movieId);
        }

        return redirect()->back()->with('message', 'Movie removed from watchlist');
    }
}
