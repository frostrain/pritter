<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;

class HomeController extends Controller
{
    public function index()
    {
        $lists = Tweet::with('user', 'user.profile_image', 'media')
               ->where('is_following_author', true)
               ->orderBy('id', 'desc')->paginate(50);

        return view('home.index', compact('lists'));
    }
}