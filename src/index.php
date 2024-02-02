<?php

session_start();

$dsn = "sqlite: data.db";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, null, null, $options);

$errorMessage2 = '';

// Fetch tous les todos de la base de données
$query = $pdo->prepare("SELECT * FROM todo ORDER BY ordre");
$query->execute();
$todos = $query->fetchAll(PDO::FETCH_ASSOC);

// Ajouter une nouvelle tâche
if (isset($_POST['addButton'])) {
    $newTodo = $_POST['inputName'];
    if (!empty($newTodo)) {
        $insertData = $pdo->prepare('INSERT INTO todo (name, expiration, completed, ordre) VALUES (:nom, :date, :completed, :ordre)');
        $insertData->execute(['nom' => $newTodo, 'date' => $_POST["todoDate"], 'completed' => 0, 'ordre' => count($todos)]);
        header('Location: index.php');
        exit();
    }
}

// Supprimer une tâche
if (isset($_POST['deleteButton'])) {
    $idToDelete = $_POST['deleteButton'];
    $deleteData = $pdo->prepare('DELETE FROM todo WHERE id = :id');
    $deleteData->execute(['id' => $idToDelete]);
    header('Location: index.php');
    exit();
}

// edit the tasks
if (isset($_POST["editButton"], $_POST["editedText"])){
    $idToEdit = $_POST['editButton'];
    $editedText = $_POST["editedText"];
    $updateData = $pdo->prepare('UPDATE todo SET name = :name WHERE id = :id');
    $updateData->execute(['name' => $editedText, 'id' => $idToEdit]);
    header('Location: index.php');
    exit();
}

// Move tasks UP
if (isset($_POST['moveUpButton'])) {
    $idToMoveUp = $_POST['moveUpButton'];
    $indexToMoveUp = array_search($idToMoveUp, array_column($todos, 'id'));

    if ($indexToMoveUp > 0) {
        $temp = $todos[$indexToMoveUp];
        $todos[$indexToMoveUp] = $todos[$indexToMoveUp - 1];
        $todos[$indexToMoveUp - 1] = $temp;

        // Mettre à jour la base de données avec la nouvelle ordonnance
        foreach ($todos as $index => $todo) {
            $updateOrder = $pdo->prepare('UPDATE todo SET ordre = :order WHERE id = :id');
            $updateOrder->execute(['order' => $index, 'id' => $todo['id']]);
        }

        header('Location: index.php');
        exit();
    }
}

// move tasks Down
if (isset($_POST['moveDownButton'])) {
    $idToMoveDown = $_POST['moveDownButton'];
    $indexToMoveDown = array_search($idToMoveDown, array_column($todos, 'id'));

    if ($indexToMoveDown < count($todos) - 1) {
        $temp = $todos[$indexToMoveDown];
        $todos[$indexToMoveDown] = $todos[$indexToMoveDown + 1];
        $todos[$indexToMoveDown + 1] = $temp;


        foreach ($todos as $index => $todo) {
            $updateOrder = $pdo->prepare('UPDATE todo SET ordre = :order WHERE id = :id');
            $updateOrder->execute(['order' => $index, 'id' => $todo['id']]);
        }

        header('Location: index.php');
        exit();
    }
}

if (isset($_POST['todoCompleted'])) {
    $idCompleted = $_POST['todoCompleted'];
    $updateCompleted = $pdo->prepare('UPDATE todo SET completed = NOT completed WHERE id = :id');
    $updateCompleted->execute(['id' => $idCompleted]);
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>ToDo App</title>
</head>
<body>
<h1>Add the tasks</h1>
<form method="post" action="index.php">
    <label class="inputContainer">
        <input class="inputTasks" type="text" name="inputName" />
    </label>
    <div class="submitButton">
        <button class="buttonSubmit" type="submit" name="addButton">submit</button>
    </div>
</form>
<form class="formulary" method="post" action="index.php">
    <?php foreach ($todos as $todo): ?>
        <div class="todo">
            <button type="submit" name="editButton" value="<?= $todo['id'] ?>">Editer</button>
            <label>
                <input type="text" class="editable <?= $todo['completed'] ? 'completed-true' : 'completed-false' ?>" value="<?= htmlspecialchars($todo['name']) ?>" name="editedText">
            </label>
            <div class="todo-controls">
                <button type="submit" name="deleteButton" value="<?= $todo['id'] ?>">Delete</button>
                <button type="submit" name="moveUpButton" value="<?= $todo['id'] ?>">Move Up</button>
                <button type="submit" name="moveDownButton" value="<?= $todo['id'] ?>">Move Down</button>
                <button type="submit" name="todoCompleted" value="<?= $todo['id'] ?>">todo Completed <?= $todo['completed'] ? 'non complétée' : 'complétée' ?></button>
            </div>
        </div>
    <?php endforeach; ?>
</form>
</body>
</html>
