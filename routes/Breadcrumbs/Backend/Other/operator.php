<?php
Breadcrumbs::for('backend.operator.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.operator'), route('backend.operator.index'));
});
