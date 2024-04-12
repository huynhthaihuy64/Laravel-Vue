<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Services\Comment\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $commentService;
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }
    public function commentProduct(int $id) {
        return CommentResource::collection($this->commentService->commentProduct($id))->resource;
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
    	$request->validate([
            'body'=>'required',
        ]);
   
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        if(!isset($input['parent_id'])){
            $input['parent_id'] = 0;
        }
   
        return $this->commentService->store($input);
    }
}
