<?php
Breadcrumbs::for('frontend.incident.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Incident'), route('frontend.incident.index'));
});

Breadcrumbs::for('frontend.incident.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Incident' ), route('frontend.incident.index'));
    $breadcrumbs->push(__('Create Incident' ), route('frontend.incident.create'));
});

Breadcrumbs::for('frontend.incident.show', function ($breadcrumbs, $incident) {
    $breadcrumbs->parent('frontend.incident.index');
    $breadcrumbs->push(__('Show Incident'), route('frontend.incident.show', $incident));
});
