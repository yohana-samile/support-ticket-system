<?php
Breadcrumbs::for('backend.report.by_client', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.reports'), route('backend.report.index'));
    $breadcrumbs->push(__('label.by_client'), route('backend.report.by_client'));
});

Breadcrumbs::for('backend.report.list_by_client', function ($breadcrumbs, $client) {
    $breadcrumbs->parent('backend.report.by_client');
    $breadcrumbs->push(__('label.list_by_client'), route('backend.report.list_by_client', $client));
});
