<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\SchedulePost;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashBoardController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('User/Dashboard');
    }

    public function index()
    {
        $channels = Channel::where('user_id', 1)
            ->get();
        $posts = SchedulePost::where('user_id', 1)
            ->with(['channels.channel', 'media'])
            ->latest()
            ->get();

        return Inertia::render('User/Posts/Index', [
            'channels' => $channels,
            'posts' => $posts
        ]);
    }

    public function create()
    {
        $channels = Channel::where('user_id', 1)
            ->get();
        return Inertia::render('User/Posts/Create', [
            'channels' => $channels
        ]);
    }

    public function edit(SchedulePost $post)
    {
        $post->load(['media', 'channels.channel']);

        // $channels = Channel::where('user_id', 1)->get();

        return $post;
        return Inertia::render('User/Posts/Edit', [
            'post'     => $post,
            // 'channels' => $channels,
        ]);
    }

    public function store(Request $request)
    {
        // return inertia('Dashboard');
    }
}
