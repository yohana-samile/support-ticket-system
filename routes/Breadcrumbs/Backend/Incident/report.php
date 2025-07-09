<?php
Breadcrumbs::for('backend.report.reports', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Reports'), route('backend.report.reports'));
});


