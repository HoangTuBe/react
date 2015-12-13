<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentController extends Controller
{
    public function index()
    {
      return Comment::all()->toArray();
    }

    public function store(Request $request)
    {
      \Log::info($request->input('author'));
      $comment = new Comment;
      $comment->content = $request->input('content');
      $comment->author = $request->input('author');
      $comment->save();

      return $comment->toArray();
    }
}
