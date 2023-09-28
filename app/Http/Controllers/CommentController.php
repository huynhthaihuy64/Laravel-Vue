<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function commentProduct(int $id) {
        $post=Product::find($id);
        $comments = Comment::where(['product_id'=>$post->id,'parent_id'=>0])
        ->orderBy('created_at','asc')
        ->with('replies.replies','replies.user','user')
        ->get();
        $listComments = CommentResource::collection($comments)->resource;
        return $listComments;
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$request->validate([
            'body'=>'required',
        ]);
   
        $input = $request->all();
        $input['user_id'] = auth()->user()->id;
        if(!isset($input['parent_id'])){
            $input['parent_id'] = 0;
        }
        $comment = Comment::create($input);
   
        return $comment;
    }
}
