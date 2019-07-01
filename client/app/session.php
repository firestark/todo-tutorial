<?php

namespace firestark;

class session
{
    public function __construct ( )
    {
        session_start ( );
        $this->initiliaze ( 'flash' );
        $this->initiliaze ( 'deprecated' );        
        $this->deprecate ( );
    }

    public function get ( string $key, $default = null )
    {
        return $_SESSION [ $key ] ??
            ( $_SESSION [ 'flash' ] [ $key ] ??
            ( $_SESSION [ 'deprecated' ] [ $key ] ?? $default ) );
    }

    public function set ( string $key, $value )
    {
        $_SESSION [ $key ] = $value;
    }

    public function flash ( string $key, $value )
    {
        $_SESSION [ 'flash' ] [ $key ] = $value;
    }

    public function has ( string $key ) : bool
    {
        return 
            isset ( $_SESSION [ $key ] ) || 
            isset ( $_SESSION [ 'flash' ] [ $key ] ) || 
            isset ( $_SESSION [ 'deprecated' ] [ $key ] );
    }

    private function deprecate ( )
    {            
        foreach ( $_SESSION [ 'flash' ] as $key => $value )
        {
            unset ( $_SESSION [ 'flash' ] [ $key ] );
            $_SESSION [ 'deprecated' ] [ $key ] = $value;
        }
    }

    private function initiliaze ( string $key )
    {
        if ( ! isset ( $_SESSION [ $key ] ) )
            $_SESSION [ $key ] = [ ];
    }

    public function __destruct ( )
    {
        unset ( $_SESSION [ 'deprecated' ] );
    }
}