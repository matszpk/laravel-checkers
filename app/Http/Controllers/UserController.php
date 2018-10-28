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

    //
    public function index()
    {
        return view('users', [ 'data' => User::orderBy('name')->paginate(10) ]);
    }

    public function getUser(int $id)
    {
        return view('user', [ 'data' => User::find($id) ]);
    }
}
