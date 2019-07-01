# Todo application

In this tutorial we are going to create a small todo application using firestark. This tutorial is supposed to be a beginner introduction to using firestark and will give you a clear idea of the architecture that firestark uses.

## The application

The todo application is going to keep a list of to-dos. These to-dos will have an ID, a description and a completion status. Within this application we will enforce **1 business rule** which is: `A todo description may only occur once`.



## Setup

### Prerequisites

- [Composer](https://getcomposer.org/download/)
- PHP7+

### Install firestark

First run the following command in the directory you want to create your todo application:

`composer create-project --prefer-dist firestark/project todo`



For the purposes of this example we are running this todo application using the PHP built-in web server. If you have your own development server you can use that instead. Remember though when using your own development server that firestark does not handle sub directories by default and therefor you may need to setup a v-host.



```php
php -S localhost:8000
```

> Command to run PHP built-in server



## The business logic

It's most logical to start with a procedure in the business logic. Let's start by creating the procedure to add a new todo.



Create the file `/app/procedures/i want to add a todo.php` and add the following code:

```php
<?php

when ( 'i want to add a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    if ( $manager->hasTodoWithDescription ( $todo->description ) )
        return [ 2000, [ ] ];

    $manager->add ( $todo );
    return [ 1000, [ ] ];
} ) ) ) );
```



#### A word about procedure filenames

Let's talk about the filename of the previously created procedure. The filename is: `i want to add a todo.php`. As you can see this is a filename that describes what we are going to do inside the file. You can name this file any way you like and place it in nested directories if you wish as long as the filename end in the `.php` extension and it's placed under the `/app/procedures` directory. This freedom of naming and nesting in sub-directories is a very important feature for the business logic of our application. When we name our procedures with descriptive names that make sense for the business logic of our application, then when we look into the procedures directory we can immediately see what the application can do. In other words the application clearly shows it's intent. 



#### Business rules

In the procedure above you can see a business rule that is enforced namely:  `A todo description may only occur once`. This is enforced with the: 

```php
$manager->hasTodoWithDescription ( $todo->description )
```

In the case that a todo with description already exists we don't add the todo but return the status `2000`. If the todo description does not yet exist we add the todo to the manager and return the status `1000`.



#### Follow-up

Now that we have created the procedure we can see a few things we need to create next. The first thing we can see is that this procedure takes 2 parameters of type: `\todo` and `\todo\manager`. These 2 parameters are agreements we need to create. Inside the procedure we can see that these agreements need some properties and methods. For the `\todo`  we need the property description. For the `\todo\manager` we need the methods `hasTodoWithDescription ( $description )` and `add ( \todo $todo )`. 

The second thing we can see is that we have chosen to use 2 status codes. These are the status codes `2000` and `1000`. The status codes number and their meanings are entirely up to us to decide. In this case we have chosen the status `2000` to represent the status for when a todo with given description already exists. The status `1000`  is the status for when a todo has successfully been added.



### Agreements

Let's create the `\todo` agreement. Create the file `\app\agreements\todo.php` and add the following code:

```php
<?php

class todo
{
    public $id = null;
    public $description = '';
    public $completed = false;

    function __construct ( $id, string $description, bool $completed = false )
    {
        $this->id = $id;
        $this->description = $description;
        $this->completed = $completed;
    }
}
```



Next we will create the `\todo\manager` agreement. Create the directory `/app/agreements/todo` and the create the file `/app/agreements/todo/manager.php` and add the following code:

```php
<?php

namespace todo;

use todo;

interface manager
{   
    function hasTodoWithDescription ( string $description ) : bool;
    
    function add ( todo $todo );
}
```

This `\todo\manager` is an interface. It's an interface because the todo manager is going to keep a collection of to-dos and to persist these to-dos it needs to communicate with a persistence mechanism. A persistence mechanism is a technical detail the business logic may know nothing about. With the `\todo\manager` interface the business logic tells the technical layer what it expect out of a todo manager. In contrast with the todo agreement we created above, there we simply used a class. We used a class there because the todo agreement doesn't need to be extended from the technical layer, it is self sufficient.



## The implementation logic

In the previous section we have implemented our business logic by created a procedure and 2 agreements. Now it is time to create the implementation logic so we can create an actual working application. In this section we are going to create:

- Services
- Bindings
- Statuses
- HTTP Routes
- Views



### Services

In the business logic we created the `\todo\manager` agreement which is an interface. That interface describes what methods we need to implement. In this case we need to implement the following methods:

```php
function hasTodoWithDescription ( string $description ) : bool;

function add ( todo $todo );
```



We are now going to create a class that implements that `\todo\manager` interface. Create the file `/client/services/flatfileTodoManager.php` and add the following code:

```php
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
```

> In Firestark this implementation is called a service.

This class is going to store a collection of to-dos as a serialized array inside a file. 



### Bindings

With bindings we tell the application how to instantiate agreements or how to use a particular service for an agreement. In our case we need to bind the `\todo` and `\todo\manager` agreement.

First we will create the bindings for the `\todo` agreement. Create the file `/client/bindings/todo.php` and add the following code:

```php
<?php

app::bind ( todo::class, function ( $app )
{
    return new todo (
        input::get ( 'id', uniqid ( ) ),
        input::get ( 'description', '' )
    );
} );
```

This binding tell the application that whenever we ask for a `\todo` we want to run the function we have used above to instantiate that todo. The function uses the input facade which checks the incoming request for data under the name of `id` and `description`. If those 2 pieces of data are provided it uses that to instantiate a new todo class. If those 2 pieces of data are not available it uses a default of`uniqid()` and `'' `(empty string)  to create the todo. 



Next we need a binding for the `\todo\manager`agreement. Whenever we ask for a `\todo\manager` we want to receive an instance of the `flatfileTodoManager` service. Let's create that binding now. Create the file `/client/bindings/todo-manager.php` and add the following code:

```php
<?php

app::share ( todo\manager::class, function ( $app )
{
    $file = __DIR__ . '/../storage/db/files/todos.data';
    $todos = unserialize ( file_get_contents ( $file ) );

	if ( ! is_array ( $todos ) )
		$todos = [ ];
    
    return new flatfileTodoManager ( 
        $file,
        $todos
    );
} );
```

This binding tells the application that whenever we ask for a `\todo\manager` we get back the `flatfileTodoManager`.  This binding expects the file `/client/storage/db/files/todos.data`. 



> Note the different method usage: `app::bind` and `app::share`. In the `\todo` binding we created before we used the `app::bind` method. Now we are using the `app::share` method. `Bind` runs on every single request for the instance. This means that `bind` creates a new instance every single time we ask for it. On the other hand `share` only runs once and 'caches' the result. This means that  `share`  gives back the same `flatfileTodoManager` instance every single time we request for it.



Create the directories:

- `/client/storage`
- `/client/storage/db`
- `/client/storage/db/files`



Then create the file: `/client/storage/db/files/todos.data`. This file must be left empty and will be filed by the `flatfileTodoManager` service.



### Status matchers

With statuses the business logic communicates an arbitrary meaning to the implementation logic. We have used the status codes `2000 `and `1000` in the procedure we created in the business logic section above. We need to create the status matchers for these 2 status codes. Let's create the status matcher for code `1000` first. Create the file `/client/statuses/1000 Added a todo.php` and add the following contents:

```php
<?php

status::matching ( 1000, function ( )
{
    session::flash ( 'message', 'Todo added.' );
    return redirect::to ( '/' );
} );
```

Whenever a business procedure returns a status with code `1000` we run this status matcher. This status matcher flashes a message to the session and then simply redirects us back to the `/` URI.



Next we'll create the status matcher for status code `2000`. Create the file `/client/statuses/2000 Todo with description already exists.php` and add the following contents:

```php
<?php

status::matching ( 2000, function ( )
{
	session::flash ( 'message', 'Todo description already exists.' );
    return redirect::to ( '/' );
} );
```

Whenever a business procedure returns a status with code `2000` we run this status matcher. This status matcher flashes a message to the session and then simply redirects us back to the `/` URI.



#### Status-matcher filenames

Let's talk about the filename of the previously created status-matchers. The filenames are descriptive as to what situation the status-matcher matches. You can name these files any way you like and place it in nested directories if you wish as long as the filename end in the `.php` extension and it's placed under the `/client/statuses` directory. 



## View

Firestark doesn't include a template engine by default. Instead it allows you to easily use your own template engine. In this tutorial we will use pure PHP. We will setup a small 'helper' class to turn our views into HTTP responses.



### The helper class

The helper class is going to use firestark's HTTP response factory to turn PHP views into HTTP responses. 

Create the `/client/app/view.php` and add the following code:

```php
<?php

namespace firestark;

use http\response\factory;
use http\response;

class view
{
    private $response = null;
    private $basedir = '';
    
    function __construct ( factory $response, string $basedir )
    {
        $this->response = $response;
        $this->basedir = $basedir;
    }

    function make ( string $view ) : response
    {
        return $this->response->ok ( 
            file_get_contents ( $this->basedir . '/' . $view . '.php' ) );
    }
}
```



### Binding

Next we need to add a binding to bind the view helper class we created above to the application. We will do this in the `/client/index.php`. Open `/client/index.php` and add the last line of the following code block:

```php
$app = new firestark\app;
$app->instance ( 'app', $app );
$app->instance ( 'session', new firestark\session );
$app->instance ( 'statuses', new firestark\statuses );
$app->instance ( 'request', firestark\request::capture ( ) );
$app->instance ( 'response', new http\response\factory ( firestark\response::class ) );
$app->instance ( 'redirector', new firestark\redirector ( 
    BASEURL, $app [ 'session' ]->get ( 'uri', '/' ) ) );
$app->instance ( 'router', new firestark\router );

// ADD THIS LINE ----------------------------------------------------------------------------
$app->instance ( 'view', new firestark\view ( $app [ 'response' ], __DIR__ . '/views' ) );
// ------------------------------------------------------------------------------------------
```



To make this binding work we need to create the `/client/views` directory. Create that directory now.

### Facade

Now we are going to create the view facade. Add the file `/client/facades/view.php` and add the following code:

```php
<?php

class view extends facade
{
    public static function getFacadeAccessor ( )
    {
        return 'view';
    }
}
```



### Routes

Next we need to create an HTTP route to run our procedure from.



Create the file `/client/routes/GET -add.php` with the following code:

```php
<?php

route::get ( '/add', function ( )
{
    return view::make ( 'todos.add' );
} );
```



Create the file `/client/routes/POST -.php` with the following code:

```php
<?php

route::post ( '/', function ( )
{
    return app::fulfill ( 'i want to add a todo' );
} );
```

Whenever we receive a POST request to the URI `/` we run the procedure `i want to add a todo` that we have created in the business logic section above.





## Notes

> We started by creating the logic to add a new todo. Normally this might be a weird place to start because usually you begin with the index action which most of the times shows a list of the resource. However we started from the mindset of the business logic and not from the mindset of the implementation logic. From business logic perspective it made perfect sense to start with the 'add a todo' functionality because there resides the biggest part of our business rules.





... Work in progress