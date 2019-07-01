# Laravel blade

This article explains how to add the laravel blade template engine to a firestark project.



## Setup

We are going to make use of a small package that provides us easy access to the laravel blade API. Run the following command inside the root of the firestark project:

```php
composer require jenssegers/blade
```



The blade template engine returns html to us as a string. We need to wrap that string into an HTTP response. For that we are going to write a small wrapper around the laravel view package we installed. Create the file `/client/app/view.php` and add the following code:

```php
<?php

namespace firestark;

use http\response\factory;
use Jenssegers\Blade\Blade as engine;

class view
{
    private $response, $view;

    public function __construct ( factory $response, engine $view )
    {
        $this->response = $response;
        $this->view = $view;
    }

    public function make ( string $template, array $data = [ ] ) : \http\response
    {
        $view = $this->view->make ( $template, $data );
        return $this->response->ok ( ( string ) $view );
    }
}

```

In the constructor you can see we use an instance of a `\http\response\factory`. This is a class provided by firestark and is responsible for taking a string of data and returning an HTTP response. The second argument is the view engine from the package we installed.



Now we have created the view wrapper to return HTTP responses we need to bind an instance of this view wrapper inside the application. Create the file `/client/bindings/view.php` with the following contents:

```php
<?php
    
use Jenssegers\Blade\Blade;
    
app::share ( 'view', function ( $app )
{
	new firestark\view ( 
        $app [ 'response' ], 
        new Blade ( __DIR__ . '/../views', __DIR__ . '/../storage/cache/blade' ) 
    )          
} );
```



We bind our view class under a key called view and return a new instance of the view wrapper we created above. The `$app [ 'response' ]` part gives us access to the `\http\response\factory` and is bound in `/client/index.php`. The second argument is an instance of the view engine that we installed. That view engine needs 2 arguments: A directory where we store views and a directory where it can write cache files. We will create the directories we have used in our bindings now. Those are:

- `/client/views`

- `/client/storage/cache/blade`

Make sure these directories are writeable by the application.



Finally we need to create a facade for easy access to our view engine. Create the file `/client/facades/view.php` with the following contents:

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





## Creating views

With the laravel template engine in place we can now create views inside the `/client/views` directory. To create a view for `test.blade.php` we can use the view facade. For example:



```php
<?php
    
route::get ( '/', function ( )
{
    return view::make ( 'test' );            
} );
```