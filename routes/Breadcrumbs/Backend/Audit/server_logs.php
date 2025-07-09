<?php
Breadcrumbs::for('backend.logs.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Logs'), route('backend.logs.index'));
});

/*Profile*/
Breadcrumbs::for('backend.logs.show', function ($breadcrumbs, $audit) {
    $breadcrumbs->parent('backend.logs.index');
    $breadcrumbs->push(__('Show'), route('backend.logs.show', $audit));
});
