<?php
session_start();

$error = "";


$todos = [];
if (file_exists('todos.json')) {
    $todos = json_decode(file_get_contents('todos.json'), true);
}

// add to-do
if (isset($_POST['addButton'])) {
    $newTodo = $_POST['inputName'];
    if (!empty($newTodo)) {
        $todos[] = ['text' => $newTodo, 'completed' => false];
        saveTodos($todos);
    } else{
        $error = "valeur incorrect";
    }
}

// delete to-do
if (isset($_POST['deleteButton'])) {
    $indexToDelete = $_POST['deleteButton'];
    if (isset($todos[$indexToDelete])) {
        unset($todos[$indexToDelete]);
        saveTodos($todos);
    }
}


if (isset($_POST["editButton"], $_POST["editedText"])){
    $indexToEdit = $_POST['editButton'];
    if (isset($todos[$indexToEdit])) {
        $todos[$indexToEdit]['text'] = $_POST["editedText"];
        saveTodos($todos);
    }
}



// Move the tasks up
if (isset($_POST['moveUpButton'])) {
    //verifier si y'a au min 2 éléments
    $indexToMoveUp = $_POST['moveUpButton'];
    if ($indexToMoveUp > 0) {
        $temp = $todos[$indexToMoveUp];
        $todos[$indexToMoveUp] = $todos[$indexToMoveUp - 1];
        $todos[$indexToMoveUp - 1] = $temp;
        saveTodos($todos);
    }
}
// Move the tasks Down
if (isset($_POST['moveDownButton'])) {
    //verifier si y'a au min 2 éléments
    $indexToMoveDown = $_POST['moveDownButton'];
    if ($indexToMoveDown < count($todos) - 1) {
        $temp = $todos[$indexToMoveDown];
        $todos[$indexToMoveDown] = $todos[$indexToMoveDown + 1];
        $todos[$indexToMoveDown + 1] = $temp;
        saveTodos($todos);
    }
}


// validate to-do
if (isset($_POST['todoCompleted'])) {
    $todoCompleted = $_POST['todoCompleted'];
    if (isset($todos[$todoCompleted])) {
        $todos[$todoCompleted]['completed'] = !$todos[$todoCompleted]['completed'];
        saveTodos($todos);
    }
}


function saveTodos($todos) {
    file_put_contents('todos.json', json_encode($todos, JSON_PRETTY_PRINT));
    header('Location: index.php');
    exit();
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">

</head>
<body>
<h1>Add Note</h1>
<form method="post" action="index.php">
    <label>
        <input type="text" name="inputName" />
    </label>
    <button type="submit" name="addButton">Submit</button>
</form>
<form method="post" action="index.php">
    <?php foreach ($todos as $index => $todo): ?>
        <div class="todo">
            <button type="submit" name="editButton" value="<?= $index ?>">edit</button>
            <label>
                <input type="text" class="editable <?= $todo['completed'] ? 'completed-true' : 'completed-false' ?>" value="<?= htmlspecialchars($todo['text']) ?>" name="editedText">
            </label>
            <div class="todo-controls">
                <button type="submit" name="deleteButton" value="<?= $index ?>">Delete</button>
                <button type="submit" name="moveUpButton" value="<?= $index ?>">Move Up</button>
                <button type="submit" name="moveDownButton" value="<?= $index ?>">Move Down</button>
                <button type="submit" name="todoCompleted" value="<?= $index ?>">todo Completed</button>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="buttonTri">
        <button type="submit" name="sortAZ">sort A to Z</button>
        <button type="submit" name="sortZA">sort Z to A</button>
    </div>
</form>
<?php if (!empty($error)) : ?>
    <div class="class">
        <h1 class="p-3 px-12">
            <?php echo $error; ?>
        </h1>
    </div>
<?php endif; ?>
</body>
</html>