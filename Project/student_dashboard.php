<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: Login.php');
    exit();
}

require_once 'config.php';

$student_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT class_id FROM students_classes WHERE student_id = ?");
$stmt->execute([$student_id]);
$class = $stmt->fetch();

if (!$class) {
    die("Classe non trouvée pour cet élève.");
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();


$stmt = $pdo->prepare("SELECT class_name FROM classes WHERE id = ?");
$stmt->execute([$class['class_id']]);
$class_name = $stmt->fetchColumn();

$current_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week', strtotime($current_date)));
$week_end = date('Y-m-d', strtotime('friday this week', strtotime($current_date)));

$stmt = $pdo->prepare("SELECT * FROM courses WHERE class_id = ? AND course_date BETWEEN ? AND ? ORDER BY course_date, start_time");
$stmt->execute([$class['class_id'], $week_start, $week_end]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$days_of_week = [];
for ($i = 0; $i < 5; $i++) {
    $days_of_week[] = date('Y-m-d', strtotime("$week_start +$i days"));
}

$hours = range(8, 18);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps Étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .user-menu {
            position: absolute;
            top: 0;
            right: 20px;
        }
        .user-menu button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .user-menu-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .user-menu-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .user-menu-content a:hover {
            background-color: #f1f1f1;
        }
        .user-menu:hover .user-menu-content {
            display: block;
        }
        .timetable {
            display: grid;
            grid-template-columns: 100px repeat(5, 1fr);
            grid-template-rows: 50px repeat(11, 1fr);
            gap: 5px;
            width: 100%;
            height: 100vh;
        }
        .timetable div {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .timetable .day-header {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .timetable .hour-header {
            background-color: #f0f0f0;
            text-align: center;
        }
        .timetable .course {
            background-color: #d9edf7;
        }
    </style>
</head>
<body>

    <div class="header-info">
        <h2><?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?> - Classe : <?= htmlspecialchars($class_name) ?></h2>
        <div class="user-menu">
            <button>Menu</button>
            <div class="user-menu-content">
                <a href="student_absences.php">Absences</a>
                <a href="student_notes.php">Notes</a>
                <a href="Controllers/LogoutController.php">Se déconnecter</a>
            </div>
        </div>
    </div>

    <div class="timetable">
        <div></div> 
        <?php foreach ($days_of_week as $day): ?>
            <div class="day-header"><?= date('l d/m/Y', strtotime($day)) ?></div>
        <?php endforeach; ?>

        <?php foreach ($hours as $hour): ?>
            <div class="hour-header"><?= sprintf('%02d:00', $hour) ?></div>
            <?php for ($day_index = 0; $day_index < 5; $day_index++):
                $current_day = $days_of_week[$day_index];
                $current_time = sprintf('%02d:00:00', $hour);
                $course_found = false;

                foreach ($courses as $course) {
                    if ($course['course_date'] == $current_day && $course['start_time'] <= $current_time && $course['end_time'] > $current_time) {
                        echo "<div class='course'>{$course['course_name']}<br>{$course['teacher_name']}<br>Salle: {$course['room']}</div>";
                        $course_found = true;
                        break;
                    }
                }

                if (!$course_found) {
                    echo "<div></div>";
                }
            endfor; ?>
        <?php endforeach; ?>
    </div>

    <nav style="text-align: center; margin-top: 20px;">
        <a href="?date=<?= date('Y-m-d', strtotime("$week_start -7 days")) ?>">Semaine précédente</a> |
        <a href="?date=<?= date('Y-m-d', strtotime("$week_start +7 days")) ?>">Semaine suivante</a>
    </nav>
</body>
</html>
