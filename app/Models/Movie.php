<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function watchlistMovies()
    {
        return $this->belongsToMany(WatchlistMovie::class, 'movie_watchlist_movie')
            ->withPivot('watch_status')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
