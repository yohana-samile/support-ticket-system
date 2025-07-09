<?php
Breadcrumbs::for('backend.incident.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Incident'), route('backend.incident.index'));
});

Breadcrumbs::for('backend.incident.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Incident' ), route('backend.incident.index'));
    $breadcrumbs->push(__('Create Incident' ), route('backend.incident.create'));
});

Breadcrumbs::for('backend.incident.show', function ($breadcrumbs, $incident) {
    $breadcrumbs->parent('backend.incident.index');
    $breadcrumbs->push(__('Show Incident'), route('backend.incident.show', $incident));
});
Breadcrumbs::for('backend.edit.incident', function ($breadcrumbs, $incident) {
    $breadcrumbs->parent('backend.incident.index');
    $breadcrumbs->push(__('Edit incident'), route('backend.incident.edit', $incident));
});

