<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
/*Home for Internal*/
if((access()->user())){

    Breadcrumbs::for('home', function ($breadcrumbs) {
        $breadcrumbs->push(trans('label.home'), route('home'));
    });

}

includeRouteFiles(__DIR__.'/Breadcrumbs/');
