<?php

namespace App\Listeners;

use App\Models\WatchlistMovie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateWatchlistMovie
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Verificar si el registro ya existe
        $exists = WatchlistMovie::where('user_id', $event->user->id)->exists();

        // Crear el registro solo si no existe
        if (!$exists) {
            WatchlistMovie::create(['user_id' => $event->user->id]);
        }
    }
}
