<?php

namespace firestark;


class response extends \http\response
{
	protected $headers =
    [
        'Access-Control-Allow-Origin'       => '*',
        'Content-Type'                      => 'text/html',
        'Access-Control-Allow-Headers'      => 'Origin, Accept, Content-Type, Authorization, X-Requested-With, Content-Range, Content-Disposition',
        'Firestark-Status'                 	=> 0
    ];

	public function __construct ( $content = '', int $status = 200, array $headers = [ ] )
	{
		$this->content = $content;
		$this->status = $status;

		$this->headers = array_merge ( $this->headers, $headers );
    }

    public function status ( int $number )
	{
		$this->headers [ 'Firestark-Status' ] = $number;
	}
}
