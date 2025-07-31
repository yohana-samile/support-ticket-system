<?php
Breadcrumbs::for('backend.report.by_mno', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.reports'), route('backend.report.index'));
    $breadcrumbs->push(__('label.by_mno'), route('backend.report.by_mno'));
});

Breadcrumbs::for('backend.report.list_by_mno', function ($breadcrumbs, $mno) {
    $breadcrumbs->parent('backend.report.by_mno');
    $breadcrumbs->push(__('label.tickets_by_mno'), route('backend.report.list_by_mno', $mno));
});
