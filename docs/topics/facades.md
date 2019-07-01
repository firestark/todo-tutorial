# Facades

A facade is a class that provides easy access to an object inside the application. Facades should only be used inside the implementation layer and used with care because they essentially are like a global variable.  Facades are really useful in situations where we want access to technical components but don't directly have an instance of that technical component available to us.

## Examples

```php

class route extends facade
{
    public static function getFacadeAccessor ( )
    {
        return 'router';
    }
}

```

The ``getFacadeAccessor`` function returns the name under which the implementation is registered inside the application.
The above facade makes it so we can access the router like so:


```php

route::get ( '/', function ( ) { } );

```

## How facades work

All components are registered under a name inside the application using bindings. Whenever you call a method on a facade, that facade uses the ``getFacadeAccessor`` method to resolve the component out of the application and delegates the call onto the component that got resolved out of the application. 

## Creating your own facades

All facades are located inside the ``/client/facades`` directory and are automatically loaded into the global namespace by composer.



You create a facade by creating a class inside the `/client/facades` directory and extending the `\facade` class. You then have to implement the `getFacadeAccessor` method which must return a string. That string is the key of the component you want to create a facade to.