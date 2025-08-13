<?php
Breadcrumbs::for('backend.setting.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.setting'), route('backend.setting.index'));
});
