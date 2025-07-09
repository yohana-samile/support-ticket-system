<?php
Breadcrumbs::for('backend.my_logs.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('My logs'), route('backend.my_logs.index'));
});

/*Profile*/
Breadcrumbs::for('backend.my_logs.profile', function ($breadcrumbs, $audit) {
    $breadcrumbs->parent('backend.my_logs.index');
    $breadcrumbs->push(__('Profile'), route('backend.my_logs.profile', $audit));
});
