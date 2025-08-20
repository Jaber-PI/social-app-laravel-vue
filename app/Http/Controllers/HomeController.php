<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = $request->user()->groups()
        ->with('currentUserMembership')
        ->orderByPivot('role')
        ->latest()
        ->get();

        return Inertia::render('Home', [
            'groups' => GroupResource::collection($groups)
        ]);
    }
}
