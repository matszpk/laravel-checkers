<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

trait LikelableController
{
    public function likeObject(string $likelableId)
    {
        $out = NULL;
        DB::transaction(function () use ($likelableId, &$out) {
            $data = (Self::MainModel)::findOrFail($likelableId);
            $this->authorize('giveOpinion', $data);

            $data->likes += 1;
            $data->save();
            $out = [ 'likes' => $data->likes ];
        });
        return $out;
    }
};
