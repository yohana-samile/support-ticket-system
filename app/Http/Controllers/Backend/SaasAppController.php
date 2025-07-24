<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SaasAppRequest as storeRequest;
use App\Models\SaasApp;
use App\Repositories\Backend\SaasAppRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SaasAppController extends Controller
{
    protected $saasAppRepository;

    public function __construct()
    {
        $this->saasAppRepository = app(SaasAppRepository::class);
    }

    public function index()
    {
        return view('pages.backend.saas_app.index');
    }

    public function create()
    {
        return view('pages.backend.saas_app.create');
    }

    public function store(storeRequest $request)
    {
        $saasApp = $this->saasAppRepository->store($request->validated());
        return redirect()->route('backend.saas_app.show', $saasApp->uid)->with('success', 'Ticket created successfully!');
    }

    public function show(SaasApp $saasApp)
    {
        return view('pages.backend.saas_app.profile.show', compact('saasApp'));
    }

    public function edit(SaasApp $saasApp)
    {
        return view('pages.backend.saas_app.edit', compact('saasApp'));
    }

    public function update(storeRequest $request, SaasApp $saasApp)
    {
        $saasApp = $this->saasAppRepository->update($saasApp, $request->validated());
        return redirect()->route('backend.saas_app.show', $saasApp->uid)->with('success', 'Saas App updated successfully!');
    }

    public function destroy(SaasApp $saasApp)
    {
        $this->saasAppRepository->delete($saasApp);
        return redirect()->route('backend.saas_app.index')->with('success', 'Saas App deleted successfully!');
    }

    public function getAll()
    {
        $saasApps = $this->saasAppRepository->getAll();
        return response()->json([
            'data' => $saasApps
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $results = SaasApp::query()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('abbreviation', 'ilike', "%{$search}%");
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
        return DataTables::of($this->saasAppRepository->getAll($request->all()))
            ->addColumn('name', function($saasApp) {
                return '<a href="'.route('backend.saas_app.show', $saasApp->uid).'">'.Str::limit($saasApp->name, 30).'</a>';
            })
            ->addColumn('abbreviation', function($saasApp) {
                return $saasApp->abbreviation;
            })
            ->addColumn('created_at', function($saasApp) {
                return $saasApp->created_at->diffForHumans();
            })
            ->addColumn('actions', function($saasApp) {
                $actions = '<a href="'.route('backend.saas_app.show', $saasApp->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                      <i class="fas fa-eye fa-sm"></i>
                   </a>
                   <a href="'.route('backend.saas_app.edit', $saasApp->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($saasApp->can_be_deleted) {
                    $formId = 'delete-saas_app-form-' . $saasApp->uid;

                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$saasApp->uid.'\')">
                        <i class="fas fa-trash fa-sm"></i>
                     </a>';

                    $actions .= csrf_field()
                        . method_field('DELETE')
                        . '<form id="'.$formId.'" action="'.route('backend.saas_app.destroy', $saasApp->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })->rawColumns(['name', 'abbreviation', 'status_badge', 'actions'])->make(true);
    }
}
