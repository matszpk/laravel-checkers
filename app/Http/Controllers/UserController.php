<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    // update user form
    public function editUser(int $id)
    {
        $data = User::find($id);
        $this->authorize('update', $data);
        return view('user.edit', [ 'data' => $data ]);
    }

    public function updateUser(Request $request, int $id)
    {
        $data = User::find($id);
        // authorization
        $this->authorize('update', $data);

        $valRules = [
            'name' => [ 'required', 'string', 'max:255',
                Rule::unique('users')->ignore($data->id) ],
            'password' => 'nullable|min:6|confirmed',
        ];
        $canChangeEmail = $request->user()->can('changeEmail', $data);
        if ($canChangeEmail)
            $valRules['email'] = [ 'required', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($data->id) ];

        // validation
        $this->validate($request, $valRules);

        $data->name = $request->input('name');

        if ($canChangeEmail)
            // only if can change email
            $data->email = $request->input('email');

        if ($request->input('password') != NULL)
            $data->password = bcrypt($request->input('password'));
        $data->save();
        return redirect('/user/' . $id);
    }
}
