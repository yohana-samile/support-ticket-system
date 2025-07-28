<?php
Breadcrumbs::for('backend.report.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.report'), route('backend.report.index'));
});
