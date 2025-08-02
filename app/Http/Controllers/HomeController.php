<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('author', 'attachments')->withCount('reactions','comments')
            ->with(['reactions' => function ($query) {
                $query->where('user_id', Auth::id());
            }])->latest()->get();

        return Inertia::render('Home', [
            'posts' => PostResource::collection($posts),
        ]);
    }
}
