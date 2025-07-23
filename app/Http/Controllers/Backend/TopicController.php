<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Topics\StoreTopicRequest as storeRequest;
use App\Http\Requests\Backend\Topics\UpdateTopicRequest as updateRequest;
use App\Models\Topic;
use App\Repositories\Backend\SaasAppRepository;
use App\Repositories\Backend\TopicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TopicController extends Controller
{
    protected $topicRepo;
    protected $saasRepo;

    public function __construct()
    {
        $this->topicRepo = app(TopicRepository::class);
        $this->saasRepo = app(SaasAppRepository::class);
    }

    public function index()
    {
        $topics = $this->topicRepo->getAll();
        return view('pages.backend.topics.main.index', compact('topics'));
    }

    public function create()
    {
        $saasApps = $this->saasRepo->getAll();
        return view('pages.backend.topics.main.create', compact('saasApps'));
    }

    public function edit(Topic $topic)
    {
        return view('pages.backend.topics.main.edit', compact('topic'));
    }

    public function store(storeRequest $request)
    {
        $this->topicRepo->store($request->validated());
        return redirect()->back()->with('success', 'Topic created successfully');
    }

    public function show(Topic $topic)
    {
        return view('pages.backend.topics.main.profile.show', compact('topic'));
    }

    public function update(updateRequest $request, Topic $topicUid)
    {
        $this->topicRepo->update($topicUid, $request->validated());
        return redirect()->route('backend.topic.index')->with('success', 'Topic updated successfully');
    }

    public function destroy(Topic $topicUid)
    {
        $this->topicRepo->delete($topicUid);
        return redirect()->route('backend.topic.index')->with('success', 'Topic deleted successfully');
    }

    public function getAll()
    {
        return response()->json(['data' => $this->topicRepo->getAll()]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $results = Topic::query()
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

    public function getByService($serviceId): JsonResponse
    {
        return response()->json(['data' => $this->topicRepo->getByServiceId($serviceId)]);
    }

    public function getAllForDt(Request $request)
    {
        return DataTables::of($this->topicRepo->getAll($request->all()))
            ->addColumn('topic_name', function($topic) {
                return '<a href="'.route('backend.topic.show', $topic->uid).'">'.Str::limit($topic->name, 30).'</a>';
            })
            ->addColumn('saas_app_name', function($topic) {
                return $topic->saasApp
                    ? '<a href="'.route('backend.saas_app.show', $topic->saasApp->uid).'">'.Str::limit($topic->saasApp->name, 30).'</a>'
                    : '<span class="text-muted">N/A</span>';
            })
            ->addColumn('status_badge', function($topic) {
                return getStatusBadge($topic->is_active);
            })
            ->addColumn('created_at', function($topic) {
                return $topic->created_at->diffForHumans();
            })
            ->addColumn('actions', function($topic) {
                $actions = '<a href="'.route('backend.topic.show', $topic->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.topic.edit', $topic->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($topic->can_be_deleted) {
                    $formId = 'delete-topic-form-' . $topic->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$topic->uid.'\')">
                        <i class="fas fa-trash fa-sm"></i>
                     </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.topic.destroy', $topic->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['topic_name', 'saas_app_name', 'status_badge', 'actions'])->make(true);
    }
}
