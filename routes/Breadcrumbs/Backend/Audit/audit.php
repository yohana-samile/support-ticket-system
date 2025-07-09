<?php
Breadcrumbs::for('backend.audit.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Audit logs'), route('backend.audit.index'));
});

/*Profile*/
Breadcrumbs::for('backend.audit.profile', function ($breadcrumbs, $audit) {
    $breadcrumbs->parent('backend.audit.index');
    $breadcrumbs->push(__('Profile'), route('backend.audit.profile', $audit));
});
