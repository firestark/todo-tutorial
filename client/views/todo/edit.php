<form action="/<?= $todo->id; ?>" method="POST">
    <textarea name="description" cols="30" rows="10" placeholder="description" required><?= $todo->description; ?></textarea>
    <input type="submit">
</form>