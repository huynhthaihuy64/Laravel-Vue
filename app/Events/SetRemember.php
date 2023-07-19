<?php

namespace App\Events;

class SetRemember
{
    public $remember;

    public function __construct($remember)
    {
        $this->remember = $remember;
    }
}
