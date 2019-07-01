# HTTP

## Request

To get the current request URI you can use the uri method on the request facade like so:

```php
request::uri ( );
```

## Response

### Creating a HTTP response

You can use the response facade to create HTTP responses. The following methods are available to create a HTTP response:

```php
response::ok ( 'test' );
response::created ( 'test' );
response::badRequest ( 'test' );
response::forbidden ( 'test' );
response::notFound ( 'test' );
response::notAllowed ( 'test' );
response::conflict ( 'test' );
response::error ( 'test' );
```

These methods `echo` the string to the page after setting the corresponding HTTP status code.

#### Redirect

To create a redirect response we use the redirect facade. With the redirect facade we can redirect back to the previous URI or redirect to a specified URI like so:



```php
redirect::back ( );

redirect::to ( '/uri' );
```



#### Setting custom response headers

The project's response class is located under `/client/app/response.php` in here you can add or change the response headers under the response property. 

## Session

Firestark provides a small session wrapper around the PHP session. The session wrapper provides a nice API to set, get and flash session values.

### Set and retrieve

To set a value in the session use the set method like so:

```php
session::set ( 'key', 'value' )
```



to get a session value us the get method like so:

```php
session::get ( 'key' );
```

### Flash

Sometimes you only want to pass a value on for the next request and automatically forget it after that. In this case you can use the flash method like so:

```php
session::flash ( 'message', 'Message for next request' );
```

