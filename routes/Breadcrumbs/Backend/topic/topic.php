<?php
Breadcrumbs::for('backend.topic.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.topic'), route('backend.topic.index'));
});

Breadcrumbs::for('backend.topic.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.topic' ), route('backend.topic.index'));
    $breadcrumbs->push(__('label.create' ), route('backend.topic.create'));
});

Breadcrumbs::for('backend.topic.show', function ($breadcrumbs, $topic) {
    $breadcrumbs->parent('backend.topic.index');
    $breadcrumbs->push(__('label.show'), route('backend.topic.show', $topic));
});

Breadcrumbs::for('backend.topic.edit', function ($breadcrumbs, $topic) {
    $breadcrumbs->parent('backend.topic.index');
    $breadcrumbs->push(__('label.edit'), route('backend.topic.edit', $topic));
});

