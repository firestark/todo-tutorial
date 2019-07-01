# Basic authentication

In this article we are going to use a PHP JWT package in combination with the session (for remembering the token) to create a basic authentication system.



## Prerequisites

- Working firestark application
- [Composer](https://getcomposer.org/)



## Model

We will model the authentication process by checking user credentials via a user manager. On login the **user manager** checks if it has the given user-name and checks if it's given hashed password is correct. If the credentials are correct we will pass the credentials through to the **guard**. The guard 'stamps' the credentials and generates a **token** which the guard will remember. On subsequent requests this token is available and therefor the user is logged-in. On logout the guard will invalidate the token.

### Users with credentials

The first thing we need is to create a user with credentials. First we will create the credentials class. Create the directory `/client/app/user/`,  then create the file `/client/app/user/credentials.php` and add the following code:

```php
<?php

namespace firestark\user;

class credentials
{
    public $username, $password;

    function __construct ( string $username, string $password )
    {
        $this->username = $username;
        $this->password = hash ( 'sha256', $password );
    }
}
```



Create the file `/client/app/user.php` with the following contents: 

```php
<?php

namespace firestark;

use firestark\user\credentials;

class user
{
    public $credentials = null;

    function __construct ( credentials $credentials )
    {
        $this->credentials = $credentials;
    }
}
```



### User manager

Now we will create the user manager which keeps a list of users and exposes some methods to see whether we know a given user and if their credentials are correct.



##### Abstraction

create the file `/client/app/user/manager.php` with the following code:

```php
<?php

namespace firestark\user;

use firestark\user;

interface manager
{
    function register ( user $user );

    /**
     * Checks if the username and user credentials exist.
     */
    function has ( user $user ) : bool;

    function registered ( string $username ) : bool;
}
```



##### Implementation

We will create create a flat-file implementation of the user manager we created above. This flat-file implementation stores users in a file as serialized data.

Create the file `/client/services/flatfileUserManager.php` with the following code:

```php
<?php

use firestark\user;
use firestark\user\manager;

class flatfileUserManager implements manager
{
    private $users = [ ];
    private $file = '';

    function __construct ( string $file, array $users )
    {
        $this->file = $file;
        $this->users = $users;
    }

    function register ( user $user )
    {        
        $this->users [ $user->credentials->username ] = $user;
        $this->write ( );
    }

    function has ( user $user ) : bool
    {
        return ( 
            isset ( $this->users [ $user->credentials->username ] ) && 
            $this->users [ $user->credentials->username ]->credentials->password 
            	=== $user->credentials->password
        );
    }

    function registered ( string $username ) : bool
    {
        return isset ( $this->users [ $username ] );
    }

    private function write ( )
	{
		file_put_contents ( $this->file, serialize ( $this->users ) );
    }
}
```



### Guard

The guard is responsible for taking user credentials and producing a token with which the user can authenticate himself. The guard must then remember this token so that the user is logged-in. 

#### Abstraction

First we are going to create an abstraction class to encapsulate some methods and properties of the guard. 

Create the file `/client/app/guard.php` with the following contents:

```php
<?php

namespace firestark;

use firestark\user\credentials;

abstract class guard
{
    /**
     * @var $public
     * All the publicly accessible routes.
     */
    private $public = 
        [ 'GET' => [ '/login', '/register', '/onboarding' ]
        , 'POST' => [ '/login', '/register' ]
        ];

    /**
     * Generate and store a token for given credentials.
     * @param credentials   The credentials to generate a token from.
     * @return string       The generated token.
     */
    abstract function stamp ( credentials $credentials ) : string;

    /**
     * Check if the given token is valid.
     */
    abstract function authenticate ( string $token ) : bool;

    abstract function getToken ( ) : string;

    /**
     * Remove token.
     */
    abstract function invalidate ( );

    /**
     * Check if the guard allows access to a given request.
     * @param string $request       The application feature request.
     * @param string $token         An optional token to access the $request with. 
     */
    function allows ( request $request, string $token = '' ) : bool
    {
        if ( in_array ( $request->uri, $this->public [ $request->method ] ) )
            return true;
        
        return $this->authenticate ( $token );
    }
}
```



#### Implementation

##### The JWT package

We are going to install a JWT package with composer. This JWT package generates and checks tokens by which we are going to authenticate a user.



Open a terminal in the root directory of the firestark project and run the following command:

```php
composer require firebase/php-jwt
```



##### Using the JWT package

Now that the JWT package is installed we need to use this package to create an implementation of the abstraction that we created earlier.



Create the file `/client/services/jwtSessionGuard.php` with the following contents:

```php
<?php

use Firebase\JWT\JWT;
use firestark\user\credentials;
use firestark\guard;
use firestark\session;

class jwtSessionGuard extends guard
{
    const key = 'my jwt key';
    private $session = null;

    function __construct ( session $session )
    {
        $this->session = $session;
    }

    function stamp ( credentials $credentials ) : string
    {
        $token = JWT::encode (
            [ 'credentials' => serialize ( $credentials )
            ]
        , self::key
        );

        $this->session->set ( 'token', $token );
        return $token;
    }

    function stamped ( ) : bool
    {
        return $this->session->has ( 'token' );
    }

    function authenticate ( string $token ) : bool
    {
        try {
            JWT::decode ( $token, self::key, array ( 'HS256' ) );
            return true;
        } catch ( exception $e ) {
            return false;
        }
    }

    function invalidate ( )
    {
        $this->session->unset ( 'token' );
    }

    function getToken ( ) : string
    {
        return $this->session->get ( 'token', '' );
    }

    function current ( ) : credentials
    {
        try {
            return unserialize ( JWT::decode ( 
                $this->session->get ( 'token' ), self::key, array ( 'HS256' ) )->credentials );
        }
        catch ( exception $e ) {
            return new credentials ( '', '' );
        }
    }
}
```

## Bindings

Now it is time to bind our guard, user and credentials. We'll start with the guard. 

Create the file `/client/bindings/guard.php` and add the following contents:

```php
<?php

use firestark\guard;

app::share ( guard::class, function ( $app ) : guard
{
    return new jwtSessionGuard (
        $app [ 'session' ] 
    );
} );
```

Here we have bound the `jwtSessionGuard` implementation we created earlier as the guard class to our application. If we ever want to change from using the `jwtSessionGuard` to another implementation we can do that here.



Next create the directory `/client/bindings/user/` then create the file `/client/bindings/user/credentials.php` with the following code:

```php
<?php

use firestark\user\credentials;

app::bind ( credentials::class, function ( $app ) : credentials
{
    // Using the guard facade here
    // We will create that facade later in the Facades section.
    
    if ( guard::stamped ( ) )
        return guard::current ( );

    return new credentials (
        input::get ( 'username', '' ),
        input::get ( 'password', '' )
    );
} );

```

Here we check if the guard already has credentials stored and if so return it. Else we will create new credentials using the input provided by the user. 



Now we need to bind the user class. Create the file `/client/bindings/user.php` with the following contents:

```php
<?php

use firestark\user;
use firestark\user\credentials;

app::bind ( user::class, function ( $app )
{
    return new user ( $app [ credentials::class ] );
} );
```

We use the credentials we have bound above.



The final binding we need is the user manager binding. Create the file `/client/bindings/user_manager.php` with the following code:

```php
<?php

use firestark\user\manager;

app::share ( manager::class, function ( $app )
{
    // We need to create this file, we'll do that next:
    $file = __DIR__ . '/../storage/db/files/users.data';
    $users = unserialize ( file_get_contents ( $file ) );

	if ( ! is_array ( $users ) )
		$users = [ ];
    
    return new flatfileUserManager (
        $file,
        $users 
    );
} );
```

We need to create the file `/client/storage/db/files/users.data`.  Create the directories and that file now. The `users.data` file must be left empty. 

Here we have bound the `flatfileUserManager` we created earlier as the user manager in our application. If we ever want to change from using the `flatfileUserManager` to another implementation, for example: `mysqlUserManager`,  we can do that here.

## Facades

For easy access to the guard implementation we create a facade. Add the file `/client/facades/guard.php` and add the following contents:

```php
<?php

class guard extends facade
{
    public static function getFacadeAccessor ( )
    {
        return firestark\guard::class;
    }
}
```



For easy access to the user manager implementation we create a facade. Add the file `/client/facades/users.php` and add the following contents:

```php
<?php

use firestark\user\manager;

class users extends facade
{
    public static function getFacadeAccessor ( )
    {
        return manager::class;
    }
}

```

## Kernel

Next we need to setup the kernel so it runs the authentication. Open the `/client/app/kernel.php` file and change it's replace all it's content with the following:

```php
<?php

namespace firestark;

use http\dispatcher;
use http\request;


class kernel
{
    private $app = null;

    function __construct ( app $app )
    {
        $this->app = $app;
    }

    function handle ( request $request ) : \http\response
    {
        if ( ! $this->allows ( $request ) )
            return $this->deny ( );

        list ( $task, $arguments ) = $this->app [ 'dispatcher' ]->match ( 
            $request->method, $request->uri );
        
        // setting the arguments matched from the router onto the http request object
        // so they can be used throughout the app from the input facade
        foreach ( $arguments as $key => $value )
            \input::set ( $key, $value );
        
        return call_user_func_array ( $task, $arguments );
    }

    private function allows ( request $request ) : bool
    {
        $token = $this->app [ guard::class ]->getToken ( );
        return ( $this->app [ guard::class ]->allows ( $request, $token ) );
    }

    private function deny ( ) : \http\response
    {
        $this->app [ 'session' ]->set ( 'intended', $this->app [ 'request' ]->uri ( ) );
        return $this->app [ 'redirector' ]->to ( '/login' );
    }
}
```

We have added the check to see if the guard allows the current request. If not we redirect the user to the login page.



## Routes

The next thing to do is implement the login and registration routes. These routes tie everything together. By talking to the user manager and the guard these routes register a new user or log an existing user in.



```php
<?php

route::get ( '/login', function ( )
{
   // return login page
} );
```



```php
<?php

route::get ( '/logout', function ( )
{
    guard::invalidate ( );
    
    session::flash ( 'message', 'Logged out.' );
    return redirect::to ( '/login' );
} );
```



```php
<?php

route::get ( '/register', function ( )
{
    return view::make ( 'login-register' );
} );
```



```php
<?php

use firestark\user;
use firestark\user\manager;

route::post ( '/login', function ( )
{
    $user = app::make ( user::class );
    
    if ( ! users::has ( $user ) )
    {
        session::flash ( 'message', 'Invalid credentials' );
        return redirect::back ( );
    }

    guard::stamp ( $user->credentials );
    session::flash ( 'message', 'Logged in.' );
    return redirect::to ( session::get ( 'intended', '/' ) );
} );
```



```php
<?php

use firestark\user;
use firestark\user\credentials;
use firestark\user\manager;

route::post ( '/register', function ( )
{
    $user = app::make ( user::class );

    if ( users::registered ( $user->credentials->username ) )
    {
        session::flash ( 'message', 'Username already exists.' );
        return redirect::back ( );
    }

    users::register ( $user );

    guard::stamp ( $user->credentials );
    return redirect::to ( '/' );
} );
```



## View

The final thing that is left to do is create the login and registration views with inputs for `username` and `password`. Make sure that the name attribute for the input fields match the keys we used in our credentials binding we created in the binding section. This means we need an input with name: `username` and an input with name `password` for both the login and registration pages.



```php
return new credentials (
    input::get ( 'username', '' ),
    input::get ( 'password', '' )
);
```

> Code from the credentials binding

## Conclusion

With this setup you have