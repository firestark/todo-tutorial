<?php

status::matching ( 1007, function ( )
{
    session::flash ( 'message', 'Updated todo.' );
    return redirect::to ( '/' );
} );