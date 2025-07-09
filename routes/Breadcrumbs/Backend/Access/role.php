<?php
    Breadcrumbs::for('backend.role.index', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push(__('Roles'), route('backend.role.index'));
    });
    /*Create*/
    Breadcrumbs::for('backend.role.create', function ($breadcrumbs) {
        $breadcrumbs->parent('backend.role.index');
        $breadcrumbs->push(__('Create'), route('backend.role.create'));
    });
    /*Edit*/
    Breadcrumbs::for('backend.role.edit', function ($breadcrumbs, $role) {
        $breadcrumbs->parent('backend.role.profile', $role);
        $breadcrumbs->push(__('Edit'), route('backend.role.edit', $role));
    });

    /*Profile*/
    Breadcrumbs::for('backend.role.profile', function ($breadcrumbs, $role) {
        $breadcrumbs->parent('backend.role.index');
        $breadcrumbs->push(__('Profile'), route('backend.role.profile', $role));
    });


    /*role users */
    Breadcrumbs::for('backend.role.users', function ($breadcrumbs, $role) {
        $breadcrumbs->parent('backend.role.index');
        $breadcrumbs->push(__('Role users'), route('backend.role.users', $role));
    });
