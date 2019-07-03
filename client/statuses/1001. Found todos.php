<?php

use function compact as with;

status::matching ( 1001, function ( array $todos )
{
    return view::make ( 'todo/list', with ( 'todos' ) );
} );