<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new firestark\app;
$app->instance ( 'app', $app );
$app->instance ( 'session', new firestark\session );
$app->instance ( 'statuses', new firestark\statuses );
$app->instance ( 'request', firestark\request::capture ( ) );
$app->instance ( 'response', new http\response\factory ( firestark\response::class ) );
$app->instance ( 'redirector', new firestark\redirector ( '', $app [ 'session' ]->get ( 'uri', '/' ) ) );
$app->instance ( 'router', new firestark\router );
$app->instance ( 'view', new firestark\view ( $app [ 'response' ], __DIR__ . '/views' ) );

facade::setFacadeApplication ( $app );

including ( __DIR__ . '/routes' );
including ( __DIR__ . '/bindings' );
including ( __DIR__ . '/statuses' );
including ( __DIR__ . '/../app/procedures' );


$dispatcher = new http\dispatcher ( $app [ 'router' ]->routes, $app [ 'router' ]->groups );
$kernel = new firestark\kernel ( $dispatcher );
$response = $kernel->handle ( $app [ 'request' ] );

$response->send ( );
$app [ 'session' ]->flash ( 'uri', $app [ 'request' ]->uri );