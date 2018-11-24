<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use CommentableController, LikelableController;
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public const MainModel = User::class;

    // get users table
    public function index()
    {
        return view('user.users', [ 'pag' => User::orderBy('name')->
            withCount('comments')->paginate(15) ]);
    }

    // get user - view single user
    public function getUser(string $userId)
    {
        $user = User::findOrFail($userId);
        $data = $this->getComments($user);
        $data['data'] = $user;
        return view('user.user', $data);
    }

    // get written comments for user
    public function writtenComments(string $userId)
    {
        $data = User::findOrFail($userId);
        $comments = $data->writtenComments()->orderBy('created_at', 'desc')
                ->paginate(15);
        // get commentable
        $userIds = $comments->filter(
                function($v) { return $v->commentable_type == 'App\User'; })
                ->pluck('commentable_id');
        $gameIds = $comments->filter(
                function($v) { return $v->commentable_type == 'App\Game'; })
                ->pluck('commentable_id');
        $cgames = Game::with(['player1', 'player2'])->
                find($gameIds, ['id','created_at', 'player1_id', 'player2_id'])->keyBy('id');
        $cusers = User::find($userIds, ['id','name'])->keyBy('id');
        return view('user.wcomments', [ 'data' => $data, 'cusers' => $cusers,
                'cgames' => $cgames, 'comments' => $comments ]);
    }

    // update user form
    public function editUser(string $userId)
    {
        $data = User::findOrFail($userId);
        $this->authorize('update', $data);
        return view('user.edit', [ 'data' => $data ]);
    }

    public function updateUser(Request $request, string $userId)
    {
        DB::transaction(function () use ($userId, $request) {
            $data = User::findOrFail($userId);
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
        return redirect()->route('user.user', $userId);
    }
}
