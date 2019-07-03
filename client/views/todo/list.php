<h1>Todo list</h1>

<ul>
    <?php foreach ( $todos as $todo ) : ?>
        <li><?= $todo->description; ?></li>
    <?php endforeach; ?>
</ul>
