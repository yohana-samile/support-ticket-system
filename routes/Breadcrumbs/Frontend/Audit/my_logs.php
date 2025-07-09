<?php
Breadcrumbs::for('frontend.my_logs.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('My logs'), route('frontend.my_logs.index'));
});

/*Profile*/
Breadcrumbs::for('frontend.my_logs.profile', function ($breadcrumbs, $audit) {
    $breadcrumbs->parent('frontend.my_logs.index');
    $breadcrumbs->push(__('Profile'), route('frontend.my_logs.profile', $audit));
});
