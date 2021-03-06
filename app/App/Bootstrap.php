<?php

namespace App;

class Bootstrap
{
    protected $executables;

    public function __construct(...$executables)
    {
        $this->executables = $executables;
    }

    public function run(...$args)
    {
        foreach ($this->executables as $executable) {
            call_user_func_array([$executable, 'run'], $args);
        }
    }
}
