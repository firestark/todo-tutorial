<?php

namespace firestark;

class request extends \http\request
{
    public function uri ( )
    {
        return $this->uri;
    }

    public function method ( )
    {
        return $this->method;
    }
}