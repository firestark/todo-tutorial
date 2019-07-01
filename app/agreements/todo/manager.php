<?php

namespace todo;

use todo;

interface manager
{   
    function hasTodoWithDescription ( string $description ) : bool;
    
    function add ( todo $todo );
}