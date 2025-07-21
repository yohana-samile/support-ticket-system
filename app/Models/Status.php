<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public static function getStatusesWithColors()
    {
        return self::all()->mapWithKeys(function ($status) {
            return [$status->slug => [
                'color_class' => $status->color_class,
                'text_color_class' => $status->text_color_class,
                'name' => $status->name
            ]];
        })->toArray();
    }
}
