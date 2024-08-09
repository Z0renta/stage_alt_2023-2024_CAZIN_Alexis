<?php
session_start();
require_once 'config.php';
require_once 'models/User.php';
require_once 'models/Course.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: Login.php');
    exit();
}

$courseModel = new CourseModel($pdo);

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if (!$course_id) {
    die("ID du cours non fourni.");
}

$course = $courseModel->findById($course_id);

if (!$course) {
    die("Cours non trouvé.");
}

$stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name FROM users u JOIN students_classes sc ON u.id = sc.student_id WHERE sc.class_id = ?");
$stmt->execute([$course['class_id']]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_absent'])) {
    foreach ($_POST['absent_students'] as $student_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM absences WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$student_id, $course_id]);
        
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO absences (user_id, course_id, absence_date, reason) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $course_id, $course['course_date'], "Absent"]);
        }
    }
    header("Location: course_details.php?course_id=$course_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_course'])) {
    $stmt = $pdo->prepare("DELETE FROM absences WHERE course_id = ?");
    $stmt->execute([$course_id]);

    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);

    header("Location: teacher_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Cours</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .course-details {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .course-details h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #444;
        }
        .course-details p {
            font-size: 16px;
            margin: 10px 0;
        }
        .student-list {
            margin-top: 20px;
        }
        .student-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-list table th, .student-list table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .student-list table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .actions button {
            padding: 10px 20px;
            margin: 5px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions button:hover {
            background-color: #0056b3;
        }
        .actions button.cancel {
            background-color: #f44336;
        }
        .actions button.cancel:hover {
            background-color: #e53935;
        }
    </style>
    <script>
        function confirmCancellation() {
            return confirm("Êtes-vous sûr de vouloir annuler ce cours ?");
        }
    </script>
</head>
<body>
    <div class="course-details">
        <h2>Détails du Cours : <?= htmlspecialchars($course['course_name']) ?></h2>
        <p>Classe : <?= htmlspecialchars($course['class_name']) ?></p>
        <p>Date : <?= htmlspecialchars($course['course_date']) ?></p>
        <p>Heure : <?= htmlspecialchars($course['start_time'] . ' - ' . $course['end_time']) ?></p>
        <p>Salle : <?= htmlspecialchars($course['room']) ?></p>

        <form method="POST">
            <div class="student-list">
                <h3>Liste des étudiants</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['first_name']) ?></td>
                            <td><input type="checkbox" name="absent_students[]" value="<?= $student['id'] ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="actions">
                <button type="submit" name="mark_absent">Marquer Absents</button>
                <button type="submit" name="cancel_course" class="cancel" onclick="return confirmCancellation();">Annuler le Cours</button>
            </div>
        </form>
    </div>
</body>
</html>
