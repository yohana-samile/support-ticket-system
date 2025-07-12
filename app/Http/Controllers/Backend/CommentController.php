<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ticket\Ticket;
use App\Repositories\Backend\CommentRepository;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function store(Request $request, $ticketUid)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $ticket = Ticket::where('uid', $ticketUid)->firstOrFail();

        $comment = $this->commentRepository->store(
            $ticket,
            user(),
            $request->only('content')
        );

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function update(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = $this->commentRepository->find($commentId);

        /**
         * Authorization - only comment owner can update
         */
        if ($comment->user_id !== user_id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->commentRepository->update($comment, $request->only('content'));

        return redirect()->back()->with('success', 'Comment updated successfully');
    }

    public function destroy($commentId)
    {
        $comment = $this->commentRepository->find($commentId);

        /**
         * Authorization - comment owner or admin can delete
         */
        if ($comment->user_id !== user_id() && !isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $this->commentRepository->delete($comment);

        return redirect()->back()->with('success', 'Comment deleted successfully');
    }
}
