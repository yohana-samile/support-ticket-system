<?php
Breadcrumbs::for('backend.report.by_payment_channel', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(__('label.reports'), route('backend.report.index'));
    $breadcrumbs->push(__('label.by_payment_channel'), route('backend.report.by_payment_channel'));
});

Breadcrumbs::for('backend.report.list_by_payment_channel', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('backend.report.by_payment_channel');
    $breadcrumbs->push(__('label.tickets_by_payment_channel'), route('backend.report.list_by_payment_channel', $channel));
});
