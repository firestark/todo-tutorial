# Services

Agreements inside the business logic may know nothing about technical details. Because of this the agreements can not directly talk to a database. To still be able to store changes in a database we create a service inside the implementation layer that extends or implements the agreement. This service can talk to a database to store changes.

Services are placed inside the `/client/services` directory and this directory is automatically loaded into the global namespace by composer.

## Examples

```php
<?php
    
namespace todo;

use todo;

interface manager
{
    function find ( $id ) : todo;
    
    function add ( todo $todo );
    
    function edit ( todo $todo );
    
    function remove ( todo $todo );
}
```

> Example 1: Todo manager agreement



```php
class flatfileTodoManager implements todo\manager
{
    private $todos = [ ];
    private $file = '';

    function __construct ( string $file, array $todos )
    {
        $this->file = $file;
        $this->todos = $todos;
    }
    
    function find ( $id ) : todo
    {
        return $this->todos [ $id ];
    }

    function add ( todo $todo )
    {
        $this->todos [ $todo->id ] = $todo;
        $this->write ( );
    }

    function edit ( todo $todo )
    {
        $this->todos [ $todo->id ] = $todo;
        $this->write ( );
    }

    function remove ( todo $todo ) : bool
    {
        unset ( $this->todos [ $todo->id ] );
        $this->write ( );
    }

    private function write ( )
	{
		file_put_contents ( $this->file, serialize ( $this->todos ) );
    }
}
```

> Example 2: Todo manager flat-file service



Example 1 shows a todo manager agreement as an interface. Example 2 shows it's implementation which we call a service.  