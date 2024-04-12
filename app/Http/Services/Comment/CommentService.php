<?php

namespace App\Http\Services\Comment;

use App\Models\Comment;
use App\Models\Product;

class CommentService
{
    protected $comment;
    protected $product;

    public function __construct(Comment $comment, Product $product)
    {
        $this->product = $product;
        $this->comment = $comment;
    }
    public function commentProduct($id)
    {
        $post = $this->product->find($id);
        $comments = $this->comment->where(['product_id' => $post->id, 'parent_id' => 0])
            ->orderBy('created_at', 'asc')
            ->with('replies.replies', 'replies.user', 'user')
            ->get();

        return $comments;
    }

    public function store($params)
    {
        return $this->comment->create($params);
    }
}
