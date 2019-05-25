<?php

namespace App\Http\Controllers;

use App\Http\Resources\Favorite\FavoritesCollection;
use App\Models\User;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function getUserFavorites(Request $request)
    {
        $favorites = User::with(['articlesFavorites', 'commentsFavorites'])
            ->where('id', $request->user()->id)
            ->get();

        return new FavoritesCollection($favorites);
    }

    public function toggleFavorite(Request $request)
    {
        $success = false;

        switch ($request->type) {
            case 'article':
                $request->user()->articlesFavorites()->toggle([$request->id]);
                $success = true;
                break;
            case 'comment':
                $request->user()->commentsFavorites()->toggle([$request->id]);
                $success = true;
        }

        return $this->success($success);
    }


}
