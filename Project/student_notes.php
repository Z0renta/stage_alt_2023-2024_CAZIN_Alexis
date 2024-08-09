<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: Login.php');
    exit();
}

require_once 'config.php';

$student_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT n.grade, n.comments, c.course_name 
                       FROM notes n 
                       JOIN courses c ON n.course_id = c.id 
                       WHERE n.student_id = ?");
$stmt->execute([$student_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Notes</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mes Notes</h2>
        <?php if (empty($notes)): ?>
            <p>Aucune note enregistrée.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Cours</th>
                    <th>Note</th>
                    <th>Commentaires</th>
                </tr>
                <?php foreach ($notes as $note): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['course_name']) ?></td>
                        <td><?= htmlspecialchars($note['grade']) ?></td>
                        <td><?= htmlspecialchars($note['comments']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
