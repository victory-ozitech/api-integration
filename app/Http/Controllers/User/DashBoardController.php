<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Channel;
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
        return Inertia::render('User/Posts/Index', [
            'channels' => $channels
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

    public function edit(string $id)
    {
        $channels = Channel::where('user_id', 1)
            ->get();
        return Inertia::render('User/Posts/Edit', [
            'id' => $id,
            'channels' => $channels
        ]);
    }

    public function store(Request $request)
    {
        // return inertia('Dashboard');
    }
}
