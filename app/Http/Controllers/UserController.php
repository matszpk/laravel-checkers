<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get users table
    public function index()
    {
        return view('user.users', [ 'pag' => User::orderBy('name')->paginate(10) ]);
    }

    // get user - view single user
    public function getUser(int $id)
    {
        return view('user.user', [ 'data' => User::find($id) ]);
    }
}
