<?php

$todos = [];
if (file_exists('todos.json')) {
    $todos = json_decode(file_get_contents('todos.json'), true);
}


if (isset($_POST['addButton'])) {
    $newTodo = $_POST['inputName'];
    if (!empty($newTodo)) {
        $todos[] = ['text' => $newTodo, 'completed' => false];
        saveTodos($todos);
    }
}


if (isset($_POST['deleteButton'])) {
    $indexToDelete = $_POST['deleteButton'];
    if (isset($todos[$indexToDelete])) {
        unset($todos[$indexToDelete]);
        saveTodos($todos);
    }
}


if (isset($_POST['toggleCompleted'])) {
    $indexToToggleCompleted = $_POST['toggleCompleted'];
    if (isset($todos[$indexToToggleCompleted])) {
        $todos[$indexToToggleCompleted]['completed'] = !$todos[$indexToToggleCompleted]['completed'];
        saveTodos($todos);
    }
}

// Sauvegarde des tâches dans le fichier JSON
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
    <title>ToDo App</title>
    <style>
        .todo {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .todo-controls {
            display: flex;
            gap: 5px;
        }

        .editable {
            cursor: pointer;
            border-bottom: 1px dashed transparent;
        }

        .editable:focus {
            outline: none;
            border-bottom: 1px dashed black;
        }
    </style>
</head>
<body>
<h1>Add Note</h1>
<form method="post" action="index.php">
    <label>
        <input type="text" name="inputName" />
    </label>
    <button type="submit" name="addButton">Submit</button>
</form>

<?php foreach ($todos as $index => $todo): ?>
    <form method="post" action="index.php">
        <div class="todo">
            <div>
                <span class="editable" contenteditable="true"><?= $todo['text'] ?></span>
                <input type="hidden" name="editedTextIndex" value="<?= $index ?>">
            </div>
            <div class="todo-controls">
                <button type="submit" name="deleteButton" value="<?= $index ?>">Delete</button>
                <button type="submit" name="moveUpButton" value="<?= $index ?>">Move Up</button>
                <button type="submit" name="moveDownButton" value="<?= $index ?>">Move Down</button>
                <button type="submit" name="toggleCompleted" value="<?= $index ?>">Toggle Completed</button>
            </div>
        </div>
    </form>
<?php endforeach; ?>
</body>
</html>
