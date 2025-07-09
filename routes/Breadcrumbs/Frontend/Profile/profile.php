<?php
Breadcrumbs::for('frontend.profile.show', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('Profile'), route('frontend.profile.show'));
});

