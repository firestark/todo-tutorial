<?php

route::get ( '/{id}/remove', function ( )
{
    return app::fulfill ( 'i want to remove a todo' );
} );