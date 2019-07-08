<?php

route::get ( '/{id}', function ( )
{
    return app::fulfill ( 'i want to see a todo' );
} );