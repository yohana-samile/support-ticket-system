<?php
Breadcrumbs::for('backend.topic.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.topic'), route('backend.topic.index'));
});

Breadcrumbs::for('backend.topic.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.topic' ), route('backend.topic.index'));
    $breadcrumbs->push(__('label.create_topic' ), route('backend.topic.create'));
});

Breadcrumbs::for('backend.topic.show', function ($breadcrumbs, $topic) {
    $breadcrumbs->parent('backend.topic.index');
    $breadcrumbs->push(__('label.show_topic'), route('backend.topic.show', $topic));
});

Breadcrumbs::for('backend.topic.edit', function ($breadcrumbs, $topic) {
    $breadcrumbs->parent('backend.topic.index');
    $breadcrumbs->push(__('label.edit_topic'), route('backend.topic.edit', $topic));
});

