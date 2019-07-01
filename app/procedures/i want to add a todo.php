<?php

when ( 'i want to add a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    if ( $manager->hasTodoWithDescription ( $todo->description ) )
        return [ 2000, [ ] ];

    $manager->add ( $todo );
    return [ 1000, [ ] ];
} ) ) ) );