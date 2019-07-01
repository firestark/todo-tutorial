<?php

route::get ( '/', function ( )
{
    return view::make ( 'todo/list' );
} );