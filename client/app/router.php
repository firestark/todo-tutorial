<?php

namespace firestark;

use closure;
use http\route;


class router extends \http\router
{
    public function get ( string $uri, closure $task )
    {
        $this->add ( new route ( 'GET ' . $uri, $task ) );
    }

    public function post ( string $uri, closure $task )
    {
        $this->add ( new route ( 'POST ' . $uri, $task ) );
    }

    public function put ( string $uri, closure $task )
    {
        $this->add ( new route ( 'PUT ' . $uri, $task ) );
    }

    public function delete ( string $uri, closure $task )
    {
        $this->add ( new route ( 'DELETE '. $uri, $task ) );
    }
}
