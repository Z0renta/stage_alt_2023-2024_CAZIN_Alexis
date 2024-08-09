<?php
session_start();
require_once 'config.php';
require_once 'models/User.php';
require_once 'models/Class.php';
require_once 'models/Course.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: Login.php');
    exit();
}

$classModel = new ClassModel($pdo);
$courseModel = new CourseModel($pdo);

$classes = $classModel->getAllClasses();

$courses = $courseModel->getCoursesByTeacher($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_class'])) {
    $class_id = $_POST['class_id'];
    $course_id = $_POST['course_id'];

    $validCourse = false;
    foreach ($courses as $course) {
        if ($course['id'] == $course_id) {
            $validCourse = true;
            break;
        }
    }

    if ($validCourse) {
        header("Location: grade_assignment.php?class_id=$class_id&course_id=$course_id");
        exit();
    } else {
        echo "Erreur : Vous n'êtes pas autorisé à attribuer des notes pour ce cours.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir une Classe et un Cours</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #444;
        }
        .container form {
            text-align: center;
        }
        .container select {
            padding: 10px;
            margin: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }
        .container button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Choisir une Classe et un Cours pour Attribuer des Notes</h2>
        <form method="POST">
            <select name="class_id" required>
                <option value="">Sélectionnez une classe</option>
                <?php foreach ($classes as $class): ?>
                <option value="<?= htmlspecialchars($class['id']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="course_id" required>
                <option value="">Sélectionnez un cours</option>
                <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['id']) ?>"><?= htmlspecialchars($course['course_name'] . ' - ' . $course['course_date']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="select_class">Sélectionner</button>
        </form>
    </div>
</body>
</html>
