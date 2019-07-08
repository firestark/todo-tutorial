<?php

namespace todo;

use todo;

interface manager
{   
    function hasTodoWithDescription ( string $description ) : bool;
    
    function add ( todo $todo );

    function all ( ) : array;

    function has ( todo $todo ) : bool;

    function update ( todo $todo );

    function remove ( todo $todo );
}