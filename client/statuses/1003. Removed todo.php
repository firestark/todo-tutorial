<?php

status::matching ( 1003, function ( todo $todo )
{
    session::flash ( 'message', 'Todo removed.' );
    return redirect::to ( '/' );
} );