<?php

namespace firestark;

class redirector extends \http\redirector
{
	protected function respond ( string $url, int $status ) : \http\response
    {
        $response = new response ( 'Redirecting', $status );
        $response [ 'Location' ] = $url;
        return $response;
    }
}