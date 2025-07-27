<?php
Breadcrumbs::for('backend.ticket.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.ticket'), route('backend.ticket.index'));
});

Breadcrumbs::for('backend.ticket.create',function($breadcrumbs){
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.ticket' ), route('backend.ticket.index'));
    $breadcrumbs->push(__('label.create' ), route('backend.ticket.create'));
});

Breadcrumbs::for('backend.ticket.show', function ($breadcrumbs, $ticket) {
    $breadcrumbs->parent('backend.ticket.index');
    $breadcrumbs->push(__('label.show'), route('backend.ticket.show', $ticket));
});

Breadcrumbs::for('backend.ticket.edit', function ($breadcrumbs, $ticket) {
    $breadcrumbs->parent('backend.ticket.index');
    $breadcrumbs->push(__('label.edit'), route('backend.ticket.edit', $ticket));
});

