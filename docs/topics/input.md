# Input

All user input can be accessed with the input facade:

```php
input::get ( 'id' );
```



Optionally a default value can be added if the input value doesn't exist:

```php
input::get ( 'id', uniqid ( ) );
```



You can also access all input at once with the all method:

```php
input::all ( );
```



## Checking for input

You can use the has method to check if input exists:

```php
input::has ( 'name' );
```

