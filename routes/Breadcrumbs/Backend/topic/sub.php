<?php
Breadcrumbs::for('backend.subtopic.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.subtopic'), route('backend.subtopic.index'));
});

Breadcrumbs::for('backend.subtopic.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.subtopic' ), route('backend.subtopic.index'));
    $breadcrumbs->push(__('label.create' ), route('backend.subtopic.create'));
});

Breadcrumbs::for('backend.subtopic.show', function ($breadcrumbs, $subtopic) {
    $breadcrumbs->parent('backend.subtopic.index');
    $breadcrumbs->push(__('label.show'), route('backend.subtopic.show', $subtopic));
});

Breadcrumbs::for('backend.subtopic.edit', function ($breadcrumbs, $subtopic) {
    $breadcrumbs->parent('backend.subtopic.index');
    $breadcrumbs->push(__('label.edit'), route('backend.subtopic.edit', $subtopic));
});

