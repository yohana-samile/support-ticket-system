<?php
Breadcrumbs::for('backend.saas_app.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.saas_app'), route('backend.saas_app.index'));
});

Breadcrumbs::for('backend.saas_app.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.saas_app' ), route('backend.saas_app.index'));
    $breadcrumbs->push(__('label.create' ), route('backend.saas_app.create'));
});

Breadcrumbs::for('backend.saas_app.show', function ($breadcrumbs, $saasApp) {
    $breadcrumbs->parent('backend.saas_app.index');
    $breadcrumbs->push(__('label.show'), route('backend.saas_app.show', $saasApp));
});

Breadcrumbs::for('backend.saas_app.edit', function ($breadcrumbs, $saasApp) {
    $breadcrumbs->parent('backend.saas_app.index');
    $breadcrumbs->push(__('label.edit'), route('backend.saas_app.edit', $saasApp));
});

