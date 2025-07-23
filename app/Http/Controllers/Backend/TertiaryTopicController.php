<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Topics\TertiaryTopicRequest as StoreRequest;
use App\Models\TertiaryTopic;
use App\Repositories\Backend\TertiaryTopicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TertiaryTopicController extends Controller
{
    protected $tertiaryTopicRepo;

    public function __construct()
    {
        $this->tertiaryTopicRepo = app(TertiaryTopicRepository::class);
    }

    public function index()
    {
        return view('pages.backend.topics.tertiary.index');
    }

    public function create()
    {
        return view('pages.backend.topics.tertiary.create');
    }

    public function store(StoreRequest $request)
    {
        $this->tertiaryTopicRepo->store($request->validated());
        return redirect()->back()->with('success', 'Tertiary Topic created successfully');
    }

    public function edit($tertiary)
    {
        $tertiaryTopic = $this->tertiaryTopicRepo->findByUid($tertiary);
        return view('pages.backend.topics.tertiary.edit', compact('tertiaryTopic'));
    }

    public function show($tertiary)
    {
        $tertiaryTopic = $this->tertiaryTopicRepo->findByUid($tertiary);
        if (!$tertiaryTopic) {
            return redirect()->back()->with('error', 'Tertiary Topic not found');
        }
        return view('pages.backend.topics.tertiary.profile.show', compact('tertiaryTopic'));
    }


    public function update(StoreRequest $request, $tertiaryTopic)
    {
        $tertiary = $this->tertiaryTopicRepo->findByUid($tertiaryTopic);
        if (!$tertiary) {
            return redirect()->back()->with('error', 'Tertiary Topic could not be deleted');
        }

       $this->tertiaryTopicRepo->update($tertiary, $request->validated());
        return redirect()->back()->with('success', 'Tertiary Topic updated successfully');
    }

    public function destroy($tertiaryTopic)
    {
        $tertiaryTopic = $this->tertiaryTopicRepo->findByUid($tertiaryTopic);
        if (!$tertiaryTopic) {
            return redirect()->back()->with('error', 'Tertiary Topic not found');
        }
        $this->tertiaryTopicRepo->delete($tertiaryTopic);
        return redirect()->route('backend.tertiary.index')->with('success', 'Tertiary Topic deleted successfully');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $results = TertiaryTopic::query()
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

    public function getAllForDt(Request $request)
    {
        return DataTables::of($this->tertiaryTopicRepo->getAll($request->all()))
            ->addColumn('topic_name', function($tertiary) {
                return $tertiary->subtopic && $tertiary->subtopic->topic
                    ? '<a href="'.route('backend.topic.show', $tertiary->subtopic->topic->uid).'">'.Str::limit($tertiary->subtopic->topic->name, 30).'</a>'
                    : '<span class="text-muted">N/A</span>';
            })
            ->addColumn('subtopic_name', function($tertiary) {
                return $tertiary->subtopic
                    ? '<a href="'.route('backend.subtopic.show', $tertiary->subtopic->uid).'">'.Str::limit($tertiary->subtopic->name, 30).'</a>'
                    : '<span class="text-muted">N/A</span>';
            })
            ->addColumn('tertiary_name', function($tertiary) {
                return '<a href="'.route('backend.tertiary.show', $tertiary->uid).'">'.Str::limit($tertiary->name, 30).'</a>';
            })
            ->addColumn('status_badge', function($tertiary) {
                return getStatusBadge($tertiary->is_active);
            })
            ->addColumn('created_at', function($tertiary) {
                return $tertiary->created_at->diffForHumans();
            })
            ->addColumn('actions', function($tertiary) {
                $actions = '<a href="'.route('backend.tertiary.show', $tertiary->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.tertiary.edit', $tertiary->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($tertiary->can_be_deleted) {
                    $formId = 'delete-topic-form-' . $tertiary->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$tertiary->uid.'\')">
                    <i class="fas fa-trash fa-sm"></i>
                 </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.tertiary.destroy', $tertiary->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['topic_name', 'subtopic_name', 'tertiary_name', 'status_badge', 'actions'])->make(true);
    }

    public function getBySubtopic($subtopicId): JsonResponse
    {
        $tertiaryTopics = $this->tertiaryTopicRepo->getBySubtopicId($subtopicId);
        return response()->json([
            'data' => $tertiaryTopics
        ]);
    }
}
