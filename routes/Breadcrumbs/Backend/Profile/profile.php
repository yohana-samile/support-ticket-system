<?php
Breadcrumbs::for('backend.profile.show', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Profile'), route('backend.profile.show'));
});

