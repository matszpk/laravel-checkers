<?php

namespace App\Http\Controllers;

use App\User;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get users table
    public function index()
    {
        return view('user.users', [ 'pag' => User::orderBy('name')->
            withCount('comments')->paginate(15) ]);
    }

    // get user - view single user
    public function getUser(int $userId)
    {
        return view('user.user', [ 'data' => User::with(['comments' => function($query) {
                $query->orderBy('created_at', 'desc'); },
            'comments.writtenBy' ])->find($userId) ]);
    }

    // get written comments for user
    public function writtenComments(int $userId)
    {
        return view('user.wcomments',
                [ 'data' => User::with(['writtenComments' => function($query) {
                    $query->orderBy('created_at', 'desc'); },
                'comments.commentable' ])->find($userId) ]);
    }

    // update user form
    public function editUser(int $userId)
    {
        $data = User::find($userId);
        $this->authorize('update', $data);
        return view('user.edit', [ 'data' => $data ]);
    }

    public function addComment(Request $request, int $userId)
    {
        $data = User::find($userId);
        $this->authorize('giveOpinion', $data);
        // validation
        $this->validate($request, [ 'content' => 'required|string|max:30000' ]);

        $comment = new Comment(['content' => $request->input('content') ]);
        $comment->writtenBy()->associate($request->user());
        $data->comments()->save($comment);
        return back();
    }

    public function likeUser(Request $request, int $userId)
    {
        $out = NULL;
        DB::transaction(function () use ($userId, &$out) {
            $data = User::find($userId);
            $this->authorize('giveOpinion', $data);

            $data->likes += 1;
            $data->save();
            $out = [ 'likes' => $data->likes ];
        });
        return $out;
    }

    public function updateUser(Request $request, int $userId)
    {
        DB::transaction(function () use ($userId, $request) {
            $data = User::find($userId);
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
        });
        return redirect('/user/' . $userId);
    }
}
