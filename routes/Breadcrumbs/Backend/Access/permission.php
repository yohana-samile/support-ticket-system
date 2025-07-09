<?php
Breadcrumbs::for('backend.permission.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Permission'), route('backend.permission.index'));
});

