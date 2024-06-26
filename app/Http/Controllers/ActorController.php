<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ActorController extends Controller
{
    public function show(string $id)
    {
        return Inertia::render('Actor/Show', ['actorId' => $id]);
    }
}
