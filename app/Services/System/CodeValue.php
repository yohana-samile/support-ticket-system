<?php

use App\Repositories\System\CodeValueRepository;

class CodeValue
{
    public $repo;

    public function __construct()
    {
        $this->repo = new CodeValueRepository();
    }

}
