<?php

namespace App\Models\Access\Attribute;

trait ClientAttribute
{
    public function getCanBeDeletedAttribute() {
        return !$this->tickets()->exists();
    }
}
