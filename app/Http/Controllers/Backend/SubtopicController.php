<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Topics\StoreSubtopicRequest as storeRequest;
use App\Models\SubTopic;
use App\Repositories\Backend\SubTopicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SubtopicController extends Controller
{
    protected $subtopicRepo;

    public function __construct()
    {
        $this->subtopicRepo = app(SubtopicRepository::class);
    }

    public function index()
    {
        $subtopics = $this->subtopicRepo->getAll();
        return view('pages.backend.topics.sub.index', compact('subtopics'));
    }

    public function create()
    {
        return view('pages.backend.topics.sub.create');
    }

    public function edit(SubTopic $subtopic)
    {
        return view('pages.backend.topics.sub.edit', compact('subtopic'));
    }

    public function store(storeRequest $request)
    {
       $this->subtopicRepo->store($request->validated());
        return redirect()->back()->with('success', 'Subtopic created successfully');
    }

    public function show(SubTopic $subtopic)
    {
        return view('pages.backend.topics.sub.profile.show', compact('subtopic'));
    }

    public function update(storeRequest $request, SubTopic $subtopic)
    {
        $this->subtopicRepo->update($subtopic, $request->validated());
        return redirect()->route('backend.subtopic.index')->with('success', 'Subtopic updated successfully');
    }

    public function destroy(SubTopic $subtopic)
    {
        $this->subtopicRepo->delete($subtopic);
        return redirect()->route('backend.subtopic.index')->with('success', 'Subtopic deleted successfully');
    }

    public function getByTopic($topic)
    {
        return response()->json(['data' => $this->subtopicRepo->getByTopicId($topic)]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $results = SubTopic::query()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%");
                });
            })
            ->paginate(10);

        return response()->json([
            'data' => $results->items(),
            'next_page_url' => $results->nextPageUrl()
        ]);
    }

    public function showForTertiaryTopic($subTopicUid)
    {
        try {
            $subtopic = SubTopic::query()->find($subTopicUid);

            if (!$subtopic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subtopic not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'subtopic' => [
                        'id' => $subtopic->id,
                        'name' => $subtopic->name
                    ],
                    'topic' => $subtopic->topic ? [
                        'id' => $subtopic->topic->id,
                        'name' => $subtopic->topic->name
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function getAll(): JsonResponse
    {
        return response()->json(['data' => $this->subtopicRepo->getAll()]);
    }

    public function getAllForDt(Request $request)
    {
        return DataTables::of($this->subtopicRepo->getAll($request->all()))
            ->addColumn('topic_name', function($subtopic) {
                return $subtopic->topic
                    ? '<a href="'.route('backend.topic.show', $subtopic->topic->uid).'">'.Str::limit($subtopic->topic->name, 30).'</a>'
                    : '<span class="text-muted">N/A</span>';
            })
            ->addColumn('subtopic_name', function($subtopic) {
                return '<a href="'.route('backend.subtopic.show', $subtopic->uid).'">'.Str::limit($subtopic->name, 30).'</a>';
            })
            ->addColumn('status_badge', function($subtopic) {
                return getStatusBadge($subtopic->is_active);
            })
            ->addColumn('created_at', function($subtopic) {
                return $subtopic->created_at->diffForHumans();
            })
            ->addColumn('actions', function($subtopic) {
                $actions = '<a href="'.route('backend.subtopic.show', $subtopic->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.subtopic.edit', $subtopic->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($subtopic->can_be_deleted) {
                    $formId = 'delete-subtopic-form-' . $subtopic->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$subtopic->uid.'\')">
                        <i class="fas fa-trash fa-sm"></i>
                     </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.subtopic.destroy', $subtopic->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['topic_name', 'subtopic_name', 'status_badge', 'actions'])->make(true);
    }

}
