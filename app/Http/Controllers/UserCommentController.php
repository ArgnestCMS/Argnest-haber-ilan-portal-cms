<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\View\View;

class UserCommentController extends Controller
{
    public function index(): View
    {
        $comments = Comment::with('commentable')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('profile.comments', compact('comments'));
    }
}