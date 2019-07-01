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