<?php
Breadcrumbs::for('backend.report.by_saas_app', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.reports'), route('backend.report.index'));
    $breadcrumbs->push(__('label.by_saas_app'), route('backend.report.by_saas_app'));
});

Breadcrumbs::for('backend.report.list_by_saas_app', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('backend.report.by_saas_app');
    $breadcrumbs->push(__('label.list_by_saas_app'), route('backend.report.list_by_saas_app', $channel));
});
