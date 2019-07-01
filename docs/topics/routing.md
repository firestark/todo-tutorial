# Routing

A route has the responsibility to match a HTTP request and turn that into a HTTP response. A route **must** always return an HTTP response.



All files inside the ``/client/routes`` directory are automatically included. This means you can create as many `.php` files as you want inside this ``/client/routes`` and nested directories to define your routes.

## Examples

```php

route::get ( '/', function ( )
{
    return new http\response ( 'Hello' );
} );

route::post ( '/', function ( )
{
    return new http\response ( 'Hello' );
} );

route::get ( '/{id}', function ( $id )
{
	return new http\response ( ( string ) $id );
} );

```



## Route parameters

```php

route::get ( '/{name}', function ( string $name )
{
	return new http\response ( 'Hello ' . $name );
} );

```

The {name} pattern gets put into the parameter $name inside the closure.


```php

route::get ( '/{id}/{name}', function ( $name, $id )
{
	return new http\response (  );
} );

```

**Watch out**: The order of provided parameters is maintained. For the example above this means the {id} part is assigned to $name and the {name} part is assigned to $id.


## Input

```php

route::get ( '/{id}', function ( $id )
{

} );

```

Route parameters are automatically made available as request input. With the route defined in above example and a request with URI `GET /my-id` means `input::get ( 'id' ) === "my-id"`.
