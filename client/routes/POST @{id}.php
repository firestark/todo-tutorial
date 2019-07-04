<?php

route::get ( '/{id}', function ( )
{
    return app::fulfill ( 'i want to update a todo' );
} );