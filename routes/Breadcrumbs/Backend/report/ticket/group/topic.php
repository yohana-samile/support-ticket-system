<?php
Breadcrumbs::for('backend.report.by_topic', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.reports'), route('backend.report.index'));
    $breadcrumbs->push(__('label.by_topic'), route('backend.report.by_topic'));
});

Breadcrumbs::for('backend.report.list_by_topic', function ($breadcrumbs, $topic) {
    $breadcrumbs->parent('backend.report.by_topic');
    $breadcrumbs->push(__('label.list_by_topic'), route('backend.report.list_by_topic', $topic));
});
