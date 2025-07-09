<?php

namespace App\Repositories\Backend;
use App\Models\Attachment;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttachmentRepository extends  BaseRepository {
    const MODEL = Attachment::class;
    protected $model;

    public function __construct(Attachment $attachment)
    {
        $this->model = $attachment;
    }

    public function find($attachmentUid)
    {
        return $this->model->where('uid', $attachmentUid)->with('ticket')->first();
    }

    public function delete(Attachment $attachment)
    {
        return DB::transaction(function () use ($attachment) {
            Storage::delete($attachment->path);

            activity()
                ->performedOn($attachment->ticket)
                ->causedBy(auth()->user())
                ->withProperties([
                    'attachment_name' => $attachment->original_name,
                    'attachment_id' => $attachment->id
                ])
                ->log('deleted attachment');

            return $attachment->delete();
        });
    }

    public function download(Attachment $attachment)
    {
        activity()
            ->performedOn($attachment->ticket)
            ->causedBy(auth()->user())
            ->withProperties([
                'attachment_id' => $attachment->id,
                'attachment_name' => $attachment->original_name
            ])
            ->log('downloaded attachment');

        if (Storage::exists($attachment->path)) {
            return Storage::download($attachment->path, $attachment->original_name);
        }

        abort(404, 'File not found');
    }

    public function view(Attachment $attachment)
    {
        activity()
            ->performedOn($attachment->ticket)
            ->causedBy(auth()->user())
            ->withProperties([
                'attachment_id' => $attachment->id,
                'attachment_name' => $attachment->original_name
            ])
            ->log('viewed attachment');

        // Return the file response with appropriate headers
        if (!Storage::exists($attachment->path)) {
            abort(404, 'File not found');
        }

        $file = Storage::get($attachment->path);
        $type = Storage::mimeType($attachment->path);

        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Content-Disposition', 'inline; filename="' . $attachment->original_name . '"');
    }
}
