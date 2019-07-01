# Procedures

A procedure is part of the business logic and applies application specific rules decided by the business to the application. The following are examples of such rules:
- A todo with given description may only be saved once.
- A person with a bronze account has 10% price reduction on his total buyings.
- Booking a flight in holiday seasons adds an additional 15% cost on the base price.

Next to applying these rules the procedure usually calls some methods to create, read, update or delete some entities in the application. 

A procedure always returns a status with optionally some data relevant to that status. This status indicates a meaning of the results from the applied business rules. 



### Filenames

Procedures are located inside the `/app/procedures` directory. Any `.php` file inside this directory and nested directories is automatically included inside your project and therefor automatically available inside the application. This means you can name your procedure files anyway you like as long as it is placed under the `/app/procedures` directory or any nested directory inside there and the file has the `.php` suffix.

The freedom of filename and placing in nested directories of procedures is a very important feature for the business logic of an application. An application with a good architecture is one that immediately shows it's intent. When we name our procedures with descriptive names that make sense for the business logic of our application, then when we look into the procedures directory we can immediately see what the application can do. In other words the application clearly shows it's intent.

## Examples


```php
when ( 'i want to add a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    $manager->add ( $todo );
    return [ 1000, [ ] ];
} ) ) ) );
```

Above is an example of a simple procedure for adding a todo. This code registers the ``'i want to add a todo'`` string to the given closure inside the application. This closure uses an instance of the ``todo`` and ``todo\manager`` to add a todo and return the status code 1000. The technical layer is responsible to provide this procedure with a ``todo`` and ``todo\manager`` instance. The status code (1000 in this case) and it's meaning is made up by ourselves. In the case of the example above it means we successfully added a todo.

### A more sophisticated example

The previous example doesn't apply any constraint to adding todo's. The business might say we don't want to allow adding todo's with the same description. A procedure to apply this rule can look like the following:


```php
when ( 'i want to add a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    if ( $manager->has ( $todo->description ) )
        return [ 2000, [ ] ];

    $manager->add ( $todo );
    return [ 1000, [ ] ];
} ) ) ) );
```

If the ``todo\manager`` instance  has a todo with given description we return a status of 2000 and never add the todo to the manager. This status code of 2000 indicates a failure of adding the todo because a todo with given description already exists.

## Executing procedures

Procedures are stored under a name inside the application. To run a procedure we call the fulfill method with the given name on the application instance.


```php
app::fulfill ( 'i want to add a todo' );
```

The code above is an example of running the procedure: ``'i want to add a todo'`` that we have defined in the examples above. the dependencies of that procedure are automatically resolved and injected by the application. As a result of running a procedure, the status returned by that procedure gets matched by the application and the connected status matcher will run.



### Executing multiple procedures

In some cases we need multiple pieces of data coming from different procedures. In this case we call the pipe method with an array of procedure names to run:

```php
return app::pipe ( [ 
    'i want to see a goal\'s tasks',
    'i want to see my consumed protein for today'
] );
```

In this case we also have to create a special status matcher with an array of statuses. If the `'i want to see a goal\'s tasks'` returns the status `1009` and the `'i want to see my consumed protein for today'` returns the status `7009` we need to create the following status matcher:

```php
status::matching ( [ 1009, 7009 ], function ( goal $goal, int $protein )
{
	//
} );
```

## Recommendations

A recommended way of naming procedure files is to use the procedure name as the filename. For example for a procedure named: `'i want to add a todo'` you would name the file: `i want to add a todo.php`. 