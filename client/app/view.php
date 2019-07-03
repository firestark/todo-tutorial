<?php

namespace firestark;

use http\response\factory;
use http\response;

class view
{
    private $response = null;
    private $basedir = '';
    
    function __construct ( factory $response, string $basedir )
    {
        $this->response = $response;
        $this->basedir = $basedir;
    }

    function make ( string $view, array $parameters = [ ] ) : response
    {
        extract ( $parameters );
        ob_start ( );
        require $this->basedir . '/' . $view . '.php';
        return $this->response->ok ( ob_get_clean ( ) );
    }
}