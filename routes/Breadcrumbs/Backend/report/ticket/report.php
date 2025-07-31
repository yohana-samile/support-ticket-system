<?php
Breadcrumbs::for('backend.report.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.report'), route('backend.report.index'));
});

Breadcrumbs::for('backend.report.all_reports', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.reports'), route('backend.report.index'));
    $breadcrumbs->push(__('label.all_reports'), route('backend.report.all_reports'));
});
