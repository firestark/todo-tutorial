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