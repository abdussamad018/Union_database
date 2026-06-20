<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $request->validate(['locale' => 'required|in:bn,en']);

        if ($request->user()) {
            $request->user()->update(['locale' => $request->locale]);
        }

        session(['locale' => $request->locale]);

        return back();
    }
}
