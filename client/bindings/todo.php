<?php

app::bind ( todo::class, function ( $app )
{
    return new todo (
        input::get ( 'id', uniqid ( ) ),
        input::get ( 'description', '' )
    );
} );