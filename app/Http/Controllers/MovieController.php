<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\WatchlistMovie;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Movie/Index');
    }

    public function adminIndex()
    {
        $movies = Movie::withCount('watchlistMovies as in_watchlist')
        ->get();

    return Inertia::render('Admin/MoviesIndex', ['movies' => $movies]);

        // $movies = Movie::select('movies.*')
        //     ->leftJoin('movie_watchlist_movie', 'movies.id', '=', 'movie_watchlist_movie.movie_id')
        //     ->selectRaw('COUNT(movie_watchlist_movie.movie_id) as in_watchlist')
        //     ->groupBy('movies.id')
        //     ->get();

        // return Inertia::render('Admin/MoviesIndex', ['movies' => $movies]);
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
            'id' => 'required|integer|unique:movies,id', // Asegura que el id sea único en la tabla movies
            'genre' => 'nullable|string',
            'overview' => 'nullable|string',
            'poster_path' => 'nullable|string|max:255',
            'spanish_release_date' => 'nullable|date',
            'title' => 'required|string|max:255',
            'runtime' => 'required|integer|max:255',
            'director' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'vote_average' => 'nullable|numeric',
            'vote_count' => 'nullable|integer'
        ]);


        // Verificar si la película ya existe en la base de datos
        $movie = Movie::find($validatedData['id']);

        if (!$movie) {
            // Crear una nueva película con los datos de la petición
            $movie = Movie::create($validatedData);
        }
    }

    public function adminManualStore(Request $request)
    {

        $request->validate([
            'id' => 'integer|unique:movies,id', // Asegura que el id sea único en la tabla movies
            //'genre' => 'nullable|string', 
            //'overview' => 'nullable|string',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'spanish_release_date' => 'nullable|date',
            'title' => 'required|string|max:255',
            //'runtime' => 'nullable|integer|max:255',
            'director' => 'required|string|max:255',
            // 'tagline' => 'nullable|string|max:255',
            // 'vote_average' => 'nullable|numeric',
            // 'vote_count' => 'nullable|integer'
        ]);

        $data = $request->all();

        $movie = Movie::create($data);

        if ($request->hasFile('poster_path')) {
            $filename = $movie->id . '-' . $request->title . '.' . $request->poster_path->extension();
            $data['poster_path'] = "/storage/posters/$filename";
            $request->file('poster_path')->storeAs('public/posters', $filename);

            $movie->poster_path = $data['poster_path'];
            $movie->save();
        } else {
            $movie->poster_path = '/storage/posters/default.jpg';
            $movie->save();
        }
        return response()->json($movie, 201);
    }


    public function adminManualUpdate(Request $request, string $id)
    {

        $validated = $request->validate([
            'id' => 'integer|unique:movies,id',
            //'genre' => 'nullable|string', 
            //'overview' => 'nullable|string',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'spanish_release_date' => 'nullable|date',
            'title' => 'string|max:255',
            //'runtime' => 'nullable|integer|max:255',
            'director' => 'string|max:255',
            // 'tagline' => 'nullable|string|max:255',
            // 'vote_average' => 'nullable|numeric',
            // 'vote_count' => 'nullable|integer'
        ]);

        $movie = Movie::findOrFail($id);

        // Remover valores 'null' para evitar errores en columnas no nulas
        $validated = array_filter($validated, function ($value) {
            return $value !== 'null';
        });

        // Guardar el nombre de la imagen antigua
        $oldPosterPath = $movie->poster_path;

        // Actualizar los datos de la película
        $movie->fill($request->except('poster_path'));

        // Manejar la carga del póster (si se incluye en la solicitud)
        if ($request->hasFile('poster_path')) {
            // Eliminar la imagen antigua si no es la imagen por defecto
            if ($oldPosterPath && $oldPosterPath !== '/storage/posters/default.jpg') {
                $oldPosterStoragePath = str_replace('/storage', 'public', $oldPosterPath);
                Storage::delete($oldPosterStoragePath);
            }

            // Actualizar el nombre del archivo del póster
            $filename = $movie->id . '-' . $request->title . '.' . $request->poster_path->extension();
            $data['poster_path'] = "/storage/posters/$filename";

            // Almacenar el nuevo archivo del póster
            $request->file('poster_path')->storeAs('public/posters', $filename);

            // Actualizar el campo poster_path en la película
            $movie->poster_path = $data['poster_path'];
        } else if ($request->title !== $movie->getOriginal('title')) {
            // Actualizar el nombre del archivo si solo se cambia el título
            if ($oldPosterPath && $oldPosterPath !== '/storage/posters/default.jpg') {
                $oldPosterStoragePath = str_replace('/storage', 'public', $oldPosterPath);
                $extension = pathinfo($oldPosterStoragePath, PATHINFO_EXTENSION);
                $newPosterPath = "public/posters/{$movie->id}-{$request->title}.{$extension}";

                Storage::move($oldPosterStoragePath, $newPosterPath);

                $movie->poster_path = str_replace('public', '/storage', $newPosterPath);
            }
        }

        $movie->save();

        return response()->json($movie, 200);    // Or return a JSON response if you prefer
    }


    public function adminMovieDestroy(string $id)
    {
        $movie = Movie::findOrFail($id);

        // Verificar si la película está en alguna watchlist
        if ($movie->watchlistMovies()->exists()) {
            return response()->json([
                'message' => 'La película está en una watchlist y no puede ser eliminada.'
            ], 400);
        }

        // Eliminar la imagen si no es la imagen por defecto
        if ($movie->poster_path && $movie->poster_path !== '/storage/posters/default.jpg') {
            $posterPath = str_replace('/storage', 'public', $movie->poster_path);
            Storage::delete($posterPath);
        }

        // Eliminar la película
        $movie->delete();

        return response()->json([
            'message' => 'Película eliminada exitosamente.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $watchlist = WatchlistMovie::where('user_id', auth()->id())->firstOrFail();
        $isInWatchlist = $watchlist->movies()->where('movie_id', $id)->exists();

        return Inertia::render('Movie/Show', [
            'id' => $id,
            'isInWatchlist' => $isInWatchlist
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
