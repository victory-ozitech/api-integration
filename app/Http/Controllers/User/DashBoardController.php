<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
        return Inertia::render('User/Posts/Index');
    }

    public function create()
    {
        return Inertia::render('User/Posts/Create');
    }

    public function edit(string $id)
    {
        return Inertia::render('User/Posts/Edit', [
            'id' => $id
        ]);
    }

    public function store(Request $request)
    {
        // return inertia('Dashboard');
    }
}
