<?php
Breadcrumbs::for('backend.tertiary.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.tertiary_topic'), route('backend.tertiary.index'));
});

Breadcrumbs::for('backend.tertiary.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.tertiary_topic' ), route('backend.tertiary.index'));
    $breadcrumbs->push(__('label.create_tertiary_topic' ), route('backend.tertiary.create'));
});

Breadcrumbs::for('backend.tertiary.show', function ($breadcrumbs, $tertiary) {
    $breadcrumbs->parent('backend.tertiary.index');
    $breadcrumbs->push(__('label.show_tertiary_topic'), route('backend.tertiary.show', $tertiary));
});

Breadcrumbs::for('backend.tertiary.edit', function ($breadcrumbs, $tertiary) {
    $breadcrumbs->parent('backend.tertiary.index');
    $breadcrumbs->push(__('label.edit_tertiary_topic'), route('backend.tertiary.edit', $tertiary));
});

