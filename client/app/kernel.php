<?php

namespace firestark;

use http\dispatcher;
use http\request;


class kernel
{
    private $dispatcher = null;

    public function __construct ( dispatcher $dispatcher )
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle ( request $request ) : \http\response
    {
        list ( $task, $arguments ) = $this->dispatcher->match ( $request->method, $request->uri );
        
        // setting the arguments matched from the router onto the http request object
        // so they can be used throughout the app from the input facade
        foreach ( $arguments as $key => $value )
            \input::set ( $key, $value );
        
        return call_user_func_array ( $task, $arguments );
    }
}
