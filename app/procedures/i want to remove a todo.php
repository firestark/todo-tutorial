<?php

when ( 'i want to remove a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    $manager->remove ( $todo );
    return [ 1003, [ ] ];
} ) ) ) );