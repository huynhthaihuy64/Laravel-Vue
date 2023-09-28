<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentVote extends Model
{
    //
    use HasFactory,SoftDeletes;
    protected $fillable = ['comment_id', 'user_id', 'vote'];

    protected $table = "comment_user_vote";

    public $timestamps = false;
}
