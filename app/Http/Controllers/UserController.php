<?php

namespace App\Http\Controllers;

use App\User;
use App\Comment;
use App\Game;
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
    public function getUser(string $userId)
    {
        $data = User::with(['comments' => function($query) {
                $query->orderBy('created_at', 'desc'); } ])->find($userId);
        // get writers for comments
        $writerIds = $data->comments->pluck('writer_id');
        $writers = User::find($writerIds, ['id','name'])->keyBy('id');
        return view('user.user', [ 'data' => $data, 'writers' => $writers ]);
    }

    // get written comments for user
    public function writtenComments(string $userId)
    {
        $data = User::with(['writtenComments' => function($query) {
                    $query->orderBy('created_at', 'desc'); } ])->find($userId);
        // get commentable
        $userIds = $data->writtenComments->filter(
                function($v) { return $v->commentable_type == 'App\User'; })
                ->pluck('commentable_id');
        $gameIds = $data->writtenComments->filter(
                function($v) { return $v->commentable_type == 'App\Game'; })
                ->pluck('commentable_id');
        $cgames = Game::with(['player1', 'player2'])->
                find($gameIds, ['id','created_at', 'player1_id', 'player2_id'])->keyBy('id');
        $cusers = User::find($userIds, ['id','name'])->keyBy('id');
        return view('user.wcomments', [ 'data' => $data, 'cusers' => $cusers,
                'cgames' => $cgames ]);
    }

    // update user form
    public function editUser(string $userId)
    {
        $data = User::find($userId);
        $this->authorize('update', $data);
        return view('user.edit', [ 'data' => $data ]);
    }

    public function addComment(Request $request, string $userId)
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

    public function likeUser(string $userId)
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

    public function updateUser(Request $request, string $userId)
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
