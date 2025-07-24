<?php
Breadcrumbs::for('backend.client.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.client'), route('backend.client.index'));
});

Breadcrumbs::for('backend.client.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.client' ), route('backend.client.index'));
    $breadcrumbs->push(__('label.create' ), route('backend.client.create'));
});

Breadcrumbs::for('backend.client.show',function($breadcrumbs, $client){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.client' ), route('backend.client.index'));
    $breadcrumbs->push(__('label.show' ), route('backend.client.show', $client));
});

Breadcrumbs::for('backend.client.edit', function($breadcrumbs, $client){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.user' ), route('backend.client.index'));
    $breadcrumbs->push(__('label.show' ), route('backend.client.show', $client));
    $breadcrumbs->push(__('label.edit'), route('backend.client.edit', $client));
});

Breadcrumbs::for('backend.client.activity', function($breadcrumbs, $client){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.client' ), route('backend.client.index'));
    $breadcrumbs->push(__('label.show' ), route('backend.client.show', $client));
    $breadcrumbs->push(__('label.caused_activity'), route('backend.client.activity', $client));
});

