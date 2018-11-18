<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }
    
    private const ModelTransMap = [
        \App\User::class => 'error.userNotFound',
        \App\Comment::class => 'error.commentNotFound',
        \App\Game::class => 'error.gameNotFound',
        \App\Move::class => 'error.moveNotFound',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException)
        {
            if ($request->expectsJson())
                return response()->json([
                    'error' => trans(Self::ModelTransMap[$exception->getModel()]),
                    'model' => $exception->getModel()
                ], 500);
            return response()->view('errors.modelnotfound',
                [ 'errorTrans' => Self::ModelTransMap[$exception->getModel()] ], 500);
        }
        
        if (!config('app.debug'))
        {
            if ($request->expectsJson())
                return response()->json([ 'error' => 'Internal error' ], 500);
            return response()->view('errors.500', [], 500);
        }
        else
            return parent::render($request, $exception);
    }
}
