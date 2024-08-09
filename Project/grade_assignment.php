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

$class_id = $_GET['class_id'] ?? null;
$course_ids = $_GET['course_id'] ?? null;

if (!$class_id || !$course_ids) {
    die("Classe ou cours non spécifié.");
}

$teacher_courses = $courseModel->getCoursesByTeacherGrouped($_SESSION['user_id']);
$valid_course = false;

foreach ($teacher_courses as $course) {
    if (in_array($course_ids, explode(',', $course['course_ids']))) {
        $valid_course = true;
        break;
    }
}

if (!$valid_course) {
    die("Erreur : Vous n'êtes pas autorisé à attribuer des notes pour ce cours.");
}

$students = $classModel->getStudentsByClass($class_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_on = $_POST['grade_on'] ?? 20;
    $grades = $_POST['grades'] ?? [];

    foreach ($grades as $student_id => $grade) {
        $grade = min($grade, $grade_on); 
        $stmt = $pdo->prepare("INSERT INTO notes (student_id, course_id, grade) VALUES (?, ?, ?)");
        foreach (explode(',', $course_ids) as $course_id) {
            try {
                $stmt->execute([$student_id, $course_id, $grade]);
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion de la note : " . $e->getMessage();
            }
        }
    }

    header('Location: grade_confirmation.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attribuer des Notes</title>
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
        .container table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .container table, .container th, .container td {
            border: 1px solid #ddd;
        }
        .container th, .container td {
            padding: 10px;
            text-align: center;
        }
        .container input[type="number"] {
            width: 60px;
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
        <h2>Attribuer des Notes pour <?= htmlspecialchars($courseModel->findById($course_ids)['course_name']) ?></h2>
        <form method="POST">
            <label for="grade_on">Notes sur :</label>
            <input type="number" id="grade_on" name="grade_on" value="20" min="1" max="20">

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['first_name']) ?></td>
                            <td><input type="number" name="grades[<?= $student['id'] ?>]" min="0" max="20"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit">Attribuer les Notes</button>
        </form>
    </div>
</body>
</html>
