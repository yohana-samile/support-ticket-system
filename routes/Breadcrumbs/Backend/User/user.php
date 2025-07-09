<?php
Breadcrumbs::for('backend.user', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Users'), route('backend.user'));
});

/*create admin */
Breadcrumbs::for('backend.create.user',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Users' ), route('backend.user'));
    $breadcrumbs->push(__('Create user' ), route('backend.create.user'));
});

Breadcrumbs::for('backend.show.user',function($breadcrumbs, $user){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('User' ), route('backend.user'));
    $breadcrumbs->push(__('show' ), route('backend.show.user', $user));
});

Breadcrumbs::for('backend.edit.user', function($breadcrumbs, $user){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('User' ), route('backend.user'));
    $breadcrumbs->push(__('Show' ), route('backend.show.user', $user));
    $breadcrumbs->push(__('Edit user'), route('backend.edit.user', $user));
});

Breadcrumbs::for('backend.user.activity', function($breadcrumbs, $user){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('User' ), route('backend.user'));
    $breadcrumbs->push(__('Show' ), route('backend.show.user', $user));
    $breadcrumbs->push(__('Caused Activity'), route('backend.user.activity', $user));
});

