<?php
Breadcrumbs::for('backend.user.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.user'), route('backend.user.index'));
});

/*create admin */
Breadcrumbs::for('backend.user.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('users' ), route('backend.user.index'));
    $breadcrumbs->push(__('create' ), route('backend.user.create'));
});

Breadcrumbs::for('backend.user.show',function($breadcrumbs, $user){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('user' ), route('backend.user.index'));
    $breadcrumbs->push(__('show' ), route('backend.user.show', $user));
});

Breadcrumbs::for('backend.user.edit', function($breadcrumbs, $user){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('user' ), route('backend.user.index'));
    $breadcrumbs->push(__('show' ), route('backend.user.show', $user));
    $breadcrumbs->push(__('edit'), route('backend.user.edit', $user));
});

Breadcrumbs::for('backend.user.caused_activity', function($breadcrumbs, $user){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('role' ), route('backend.role.index'));
//    $breadcrumbs->push(__('user_with_role' ), route('backend.role.role_user', $user));
    $breadcrumbs->push(__('Caused Activity'), route('backend.user.caused_activity', $user));
});
