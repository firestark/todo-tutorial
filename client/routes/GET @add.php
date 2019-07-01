<?php

route::get ( '/add', function ( )
{
    return view::make ( 'todo/add' );
} );