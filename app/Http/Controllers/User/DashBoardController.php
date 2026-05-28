<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashBoardController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Dashboard');
    }

    public function create()
    {
        return "inertia('Dashboard')";
    }

    public function store(Request $request)
    {
        // return inertia('Dashboard');
    }
}
