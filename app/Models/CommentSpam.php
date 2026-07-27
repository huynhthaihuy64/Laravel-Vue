<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentSpam extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['comment_id', 'user_id'];

    protected $table = "comment_spam";

    public $timestamps = false;
}
