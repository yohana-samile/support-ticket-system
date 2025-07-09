<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\AttachmentRepository;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    protected $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    public function destroy(Request $request, $attachmentId)
    {
        $attachment = $this->attachmentRepository->find($attachmentId);
        $this->attachmentRepository->delete($attachment);
        return redirect()->back()->with('success', 'Attachment deleted successfully');
    }

    public function download($attachmentUid)
    {
        $attachment = $this->attachmentRepository->find($attachmentUid);
        return $this->attachmentRepository->download($attachment);
    }

    public function view($attachmentUid)
    {
        $attachment = $this->attachmentRepository->find($attachmentUid);
        return $this->attachmentRepository->view($attachment);
    }
}
