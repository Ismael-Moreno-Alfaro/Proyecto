<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return Inertia::render('Admin/UsersIndex', [
            'users' => $users
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        $user = User::findOrFail($user->id);
        $user->delete();
    }
}
