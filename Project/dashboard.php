<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

echo "Bienvenue, " . $_SESSION['username'] . "!<br>";
echo "Vous êtes connecté en tant qu'administrateur.";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tableau de Bord Admin</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="Controllers/LogoutController.php">Déconnexion</a></li>
        </ul>
    </nav>

    <h2>Créer un Compte Élève</h2>
    <form method="POST" action="Controllers/AuthController.php">
        <input type="hidden" name="create_student" value="1">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <label for="first_name">Prénom :</label>
        <input type="text" id="first_name" name="first_name" required>
        <label for="last_name">Nom :</label>
        <input type="text" id="last_name" name="last_name" required>
        <button type="submit">Créer Compte Élève</button>
    </form>

    <h2>Créer un Compte Professeur</h2>
    <form method="POST" action="Controllers/AuthController.php">
        <input type="hidden" name="create_teacher" value="1">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <label for="first_name">Prénom :</label>
        <input type="text" id="first_name" name="first_name" required>
        <label for="last_name">Nom :</label>
        <input type="text" id="last_name" name="last_name" required>
        <button type="submit">Créer Compte Professeur</button>
    </form>

    <h2>Créer une Classe</h2>
    <form method="POST" action="Controllers/AuthController.php">
        <input type="hidden" name="create_class" value="1">
        <label for="class_name">Nom de la Classe :</label>
        <input type="text" id="class_name" name="class_name" required>
        <button type="submit">Créer Classe</button>
    </form>

    <h2>Modifier les Classes</h2>
    <form method="POST" action="Controllers/AuthController.php">
        <input type="hidden" name="modify_class" value="1">
        <label for="class_id">Sélectionnez la Classe :</label>
        <select id="class_id" name="class_id" required>
            <?php
            require_once 'config.php';
            $stmt = $pdo->query("SELECT id, class_name FROM classes");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=\"{$row['id']}\">{$row['class_name']}</option>";
            }
            ?>
        </select>

        <label for="student_name">Nom de l'élève à ajouter :</label>
        <input type="text" id="student_name" name="student_name" required>
        <button type="submit">Ajouter Élève</button>
    </form>

    <h2>Créer un Cours</h2>
    <form method="POST" action="Controllers/AuthController.php">
        <input type="hidden" name="create_course" value="1">
        <label for="course_name">Nom du cours :</label>
        <input type="text" id="course_name" name="course_name" required>
        <label for="teacher_name">Nom du professeur :</label>
        <input type="text" id="teacher_name" name="teacher_name" required>
        <label for="class_id">Classe :</label>
        <select id="class_id" name="class_id" required>
            <?php
            require_once 'config.php';
            $stmt = $pdo->query("SELECT id, class_name FROM classes");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=\"{$row['id']}\">{$row['class_name']}</option>";
            }
            ?>
        </select>
        <label for="start_time">Heure de début :</label>
        <input type="time" id="start_time" name="start_time" required>
        <label for="end_time">Heure de fin :</label>
        <input type="time" id="end_time" name="end_time" required>
        <label for="room">Salle :</label>
        <input type="text" id="room" name="room" required>
        <label for="course_date">Date du cours :</label>
        <input type="date" id="course_date" name="course_date" required>
        <button type="submit">Créer Cours</button>
    </form>
</body>
</html>
