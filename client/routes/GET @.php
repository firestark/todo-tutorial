<?php

route::get ( '/', function ( )
{
    return app::fulfill ( 'i want to see my to-dos' );
} );