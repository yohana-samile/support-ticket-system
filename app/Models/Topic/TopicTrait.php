<?php
namespace App\Models\Topic;

trait TopicTrait
{
    public function getCanBeDeletedAttribute() {
        return !$this->tickets()->exists();
    }
}
