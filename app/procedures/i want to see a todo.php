<?php

use function compact as with;

when ( 'i want to see a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    if ( ! $manager->has ( $todo ) )
        return [ 2001, [ ] ];

    $todo = $manager->find ( $todo );
    return [ 1002, with ( 'todo' ) ];
} ) ) ) );