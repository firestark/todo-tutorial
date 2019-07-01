<?php

namespace firestark;

use ioc\container;
use function compact as with;


class app extends container
{
    private $statuses = [ ];
    private $data = [ ];

    public function fulfill ( string $request, array $payload = [ ] ) : response
    {
        list ( $status, $body ) = $this->make ( $request, $payload );
        $response = $this->call ( $this [ 'statuses' ]->match ( $status ), $body );
        $response->status ( $status );
        return $response;
    }

    function pipe ( array $procedures, array $payload = [ ] ) : response
    {
        $this->data = $payload;

        foreach ( $procedures as $procedure )
            $this->run ( $procedure, $this->data );

        $response = $this->call ( $this [ 'statuses' ]->match ( $this->statuses ), $this->data );
        $this->data = [ ];
        return $response;
    }

    public function run ( string $request, array $payload = [ ] )
    {
        list ( $status, $body ) = $this->make ( $request, $payload );
        $this->statuses [ ] = $status;
        $this->data = array_merge ( $this->data, $body );        
    }
}