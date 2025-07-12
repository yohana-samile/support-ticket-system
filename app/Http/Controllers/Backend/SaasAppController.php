<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SaasAppRequest as storeRequest;
use App\Repositories\Backend\SaasAppRepository;

class SaasAppController extends Controller
{
    protected $saasAppRepository;

    public function __construct()
    {
        $this->saasAppRepository = app(SaasAppRepository::class);
    }

    public function index()
    {
        $saasApps = $this->saasAppRepository->getAll();
        return view('pages.backend.saas_app.index', compact('saasApps'));
    }

    public function getAll()
    {
        $saasApps = $this->saasAppRepository->getAll();
        return response()->json([
            'data' => $saasApps
        ]);
    }

    public function create()
    {
        return view('pages.backend.saas_app.create');
    }

    public function store(storeRequest $request)
    {
        $ticket = $this->saasAppRepository->store($request->validated());
        return redirect()->route('backend.saas_app.show', $ticket->uid)->with('success', 'Ticket created successfully!');
    }

    public function show($saasAppUid)
    {
        $data['saasApp'] = $this->saasAppRepository->findByUid($saasAppUid);
        return view('pages.backend.saas_app.show', $data);
    }

    public function edit($saasAppUid)
    {
        $data['saasApp'] = $this->saasAppRepository->findByUid($saasAppUid);
        return view('pages.backend.saas_app.edit', $data);
    }

    public function update(storeRequest $request, $saasAppUid)
    {
        $saasAppUid = $this->saasAppRepository->update($saasAppUid, $request->validated());
        return redirect()->route('backend.saas_app.show', $saasAppUid->uid)->with('success', 'Saas App updated successfully!');
    }

    public function destroy($saasAppUid)
    {
        $this->saasAppRepository->delete($saasAppUid);
        return redirect()->route('backend.saas_app.index')->with('success', 'Saas App deleted successfully!');
    }
}
