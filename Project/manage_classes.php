<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
}

$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
$students = $pdo->query("SELECT * FROM users WHERE role = 'student'")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gérer les Classes</title>
</head>
<body>
    <h1>Gérer les Classes</h1>
    <form method="POST" action="">
        <input type="hidden" name="class_id" value="">
        <label for="class_name">Nom de la classe:</label>
        <input type="text" id="class_name" name="class_name" required>
        <button type="submit">Créer/Mettre à jour la classe</button>
    </form>

    <h2>Liste des Classes</h2>
    <ul>
        <?php foreach ($classes as $class): ?>
            <li>
                <?= htmlspecialchars($class['class_name']) ?>
                <a href="edit_class.php?id=<?= $class['id'] ?>">Modifier</a>
                <a href="delete_class.php?id=<?= $class['id'] ?>">Supprimer</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Attribuer des étudiants à des classes</h2>
    <form method="POST" action="">
        <label for="student_id">ID de l'étudiant:</label>
        <input type="number" id="student_id" name="student_id" required>
        <label for="class_id">ID de la classe:</label>
        <input type="number" id="class_id" name="class_id" required>
        <button type="submit">Attribuer</button>
    </form>

    <h3>Liste des étudiants</h3>
    <ul>
        <?php foreach ($students as $student): ?>
            <li>
                <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['last_name']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
