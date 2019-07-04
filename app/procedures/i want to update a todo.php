<?php

when ( 'i want to update a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    if ( ! $manager->has ( $todo ) )
        return [ 2001, [ ] ];

    $manager->update ( $todo );
    return [ 1007, [ ] ];
} ) ) ) );