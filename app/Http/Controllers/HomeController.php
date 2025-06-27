<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $posts = Post::with('author')->latest()->paginate(6);

        return Inertia::render('Home', [
            'posts' => PostResource::collection($posts),
        ]);
    }
}
