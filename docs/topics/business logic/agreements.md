# Agreements

Agreements are part of the business logic of your application. Agreements may contain some *non application specific business rules* that always have to be enforced. An example of that could be: A person has to be above the age of 18 to be allowed to buy products in our store. If this rule needs to be enforced for the entire business and not just this application it is a good idea to put that rule in an agreement. Agreements must have **no dependencies** on the outside world. This means that agreements can only use pure PHP functions, functions defined directly inside the business logic and other agreements. 



Agreements are classes, abstract classes and interfaces placed inside the `/app/agreements` directory. The `/app/agreements` directory is loaded into the global namespace by composer.

## Examples



```php
<?php
    
class todo
{
    public $id = null;
    public $description = '';
    
    function __construct ( $id, string $description )
    {
        $this->id = $id;
        $this->description = substr ( $description, 0, 30 );
    }
}
```

> Example 1

Example 1 models a todo and describes all the data that belongs to a todo. As an example for business logic it crops the string to a max of 30 characters.



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

> Example 2.

Example 2 models a todo manager. It declares some methods to read and manage a todo collection. This todo manager is an interface because it may not know anything about technical details. The implementation layer is responsible for implementing this interface and talking to a persistence mechanism (database, flat-file).  



```php
<?php

namespace goal;

use goal;

abstract class manager
{
    private $tasks = null;

    function __construct ( \task\manager $tasks )
    {
        $this->tasks = $tasks;
    }

    abstract function all ( ) : array;
    
    abstract function find ( $id ) : goal;

    abstract function add ( goal $goal );

    function isOverdue ( goal $goal ) : bool
    {
        return ( ! $this->isCompleted ( $goal ) and ( $goal->due < time ( ) ) ); 
    }

    function isCompleted ( goal $goal ) : bool
    {
        foreach ( $goal->tasks as $task )
            if ( ! $this->tasks->isCompleted ( $task ) )
                return false;
        
        return ! empty ( $goal->tasks );
    }

    function isDraft ( goal $goal ) : bool
    {
        return ( empty ( $goal->tasks ) and ! $this->isOverdue ( $goal ) );
    }
}
```

> Example 3

Example 3 shows a somewhat more complex example. It models a goal manager as an abstract class. In this case an abstract class is used because the functionality for `isOverdue, isCompleted and isDraft` are business logic. Also note that this agreement uses another agreement: The `task\manager`. The implementation layer is responsible for implementing the abstract methods in the class and talking to a persistence mechanism (database, flat-file).  