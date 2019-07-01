<?php

status::matching ( 1000, function ( )
{
    session::flash ( 'message', 'Todo added.' );
    return redirect::to ( '/' );
} );