<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['title'] = "Home";
        $data['breadcrumbs'] = [
            ['label' => 'Home'],
        ];
        
        $user = Auth::user();
        $data['name'] = ucwords($user->name);

        return view('home', $data);
    }
}