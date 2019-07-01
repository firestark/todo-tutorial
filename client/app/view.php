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

    function make ( string $view ) : response
    {
        return $this->response->ok ( file_get_contents ( $this->basedir . '/' . $view . '.php' ) );
    }
}