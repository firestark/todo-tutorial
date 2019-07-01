<?php

namespace firestark;

use closure;


class statuses
{
    private $matched = [ ];

    public function match ( $status ) : closure
    {
        $key = $this->encode ( $status );
        
        if ( ! $this->matches ( $key ) )
            throw new \runtimeException ( "The status code: {$this->toString($status)} has not been matched." );

        return $this->matched [ $key ];
    }

    public function matching ( $status, closure $callback )
    {
        $key = $this->encode ( $status );
        
        if ( $this->matches ( $key ) )
            throw new \runtimeException ( "The status code: {$this->toString($status)} has already been matched." );

        $this->matched [ $key ] = $callback;
    }

    public function matches ( $key ) : bool
    {
        return ( array_key_exists ( $key, $this->matched ) ) ;
    }

    private function encode ( $status ) : string
    {
        return serialize ( $status );
    }

    private function toString ( $status ) : string
    {
        if ( is_array ( $status ) )
            return '[ ' . implode ( ', ', $status ) . ' ]';
        
        return ( string ) $status;
    }
}
