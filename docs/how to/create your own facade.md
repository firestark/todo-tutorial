# create your own facade

This article takes the example of binding a configuration component to a configuration facade as an example to explain how to bind your own component to the application and create a facade for that. For more information about bindings look for the bindings topic. For more information about facades look into the facade topic.



The first thing we need is the component we want to bind. Create the file: `/client/app/configuration.php` and add the following contents to it:

```php
<?php
    
namespace firestark;
    
class configuration
{
    private $settings = [ ];
    
    function __construct ( array $settings = [ ] )
    {
        $this->settings = $settings;
    }
    
    function set ( string $key, $value )
    {
        $this->settings [ $key ] = $value;
    }
    
    function get ( string $key, $default = null )
    {
        return $this->settings [ $key ] ?? $default;
    }
    
    function remove ( string $key )
    {
        unset ( $this->settings [ $key ] );
    }
}
```



The second thing we need to do is create a binding for this component. Create the file `/client/bindings/configuration.php` with the following contents:



```php
<?php
    
app::share ( 'configuration', function ( $app )
{
    return new firestark\configuration ( );
} );
```



The last thing we need to do is create the facade class. Create the file `/client/facades/configuration.php` with the following contents:

```php
<?php

class configuration extends facade
{
    public static function getFacadeAccessor ( )
    {
        return 'configuration';
    }
}
```

Note that the `getFacadeAccessor` returned string `'configuration'` matches the binding `'configuration'` name. These have to be the same. 



This setup gives you the ability to access the configuration components method as static function calls like so:



```php
<?php
    
configuration::set ( 'view', __DIR__ . '/views' );

configuration::remove ( 'view' );

configuration::get ( 'view', '' );
```

