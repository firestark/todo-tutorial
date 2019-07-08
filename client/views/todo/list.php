<h1>Todo list</h1>

<ul>
    <?php foreach ( $todos as $todo ) : ?>
        <li><a href="/<?= $todo->id; ?>"><?= $todo->description; ?></a></li>
    <?php endforeach; ?>
</ul>
