<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();

        return view('profile.show', [
            'user' => $user->loadCount('banks'),
        ]);
    }
}

