<?php

class flatfileTodoManager implements todo\manager
{
    private $todos = [ ];
    private $file = '';

    function __construct ( string $file, array $todos )
    {
        $this->file = $file;
        $this->todos = $todos;
    }

    function all ( ) : array
    {
        return $this->todos;
    }

    function add ( todo $todo )
    {
        $this->todos [ $todo->id ] = $todo;
        $this->write ( );
    }

    function hasTodoWithDescription ( string $description ) : bool
    {
        foreach ( $this->todos as $todo )
            if ( $todo->description === $description )
                return true;
        
        return false;
    }

    private function write ( )
	{
		file_put_contents ( $this->file, serialize ( $this->todos ) );
    }
}