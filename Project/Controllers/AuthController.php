<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';
require_once '../models/User.php';
require_once '../models/Class.php';
require_once '../models/Course.php';

$userModel = new User($pdo);
$classModel = new ClassModel($pdo);
$courseModel = new CourseModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            
            if ($user['role'] === 'admin') {
                header('Location: ../dashboard.php');
            } else if ($user['role'] === 'student') {
                header('Location: ../student_dashboard.php');
            } else if ($user['role'] === 'teacher') {
                header('Location: ../teacher_dashboard.php');  
            } else {
                header('Location: ../user_dashboard.php');
            }
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
            echo $error;
        }
    }

 
    if (isset($_POST['create_student'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $role = 'student';

        $existingUser = $userModel->findByUsername($username);
        if ($existingUser) {
            $error = "Nom d'utilisateur déjà pris.";
            echo $error;
        } else {
            $userModel->create($username, $password, $first_name, $last_name, $role);
            $success = "Compte élève créé avec succès !";
            echo $success;
        }
    }

    
    if (isset($_POST['create_class'])) {
        $class_name = $_POST['class_name'];
        $classModel->create($class_name);
        $success = "Classe créée avec succès !";
        echo $success;
    }

   
    if (isset($_POST['create_teacher'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $role = 'teacher';

        $existingUser = $userModel->findByUsername($username);
        if ($existingUser) {
            $error = "Nom d'utilisateur déjà pris.";
            echo $error;
        } else {
            $userModel->create($username, $password, $first_name, $last_name, $role);
            $success = "Compte professeur créé avec succès !";
            echo $success;
        }
    }

   
    if (isset($_POST['create_course'])) {
        $course_name = $_POST['course_name'];
        $teacher_name = $_POST['teacher_name'];
        $class_id = $_POST['class_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $room = $_POST['room'];
        $course_date = $_POST['course_date'];  

       
        $teacher = $userModel->findByUsername($teacher_name);
        if (!$teacher || $teacher['role'] !== 'teacher') {
            echo "Erreur : Professeur non trouvé ou le rôle n'est pas 'teacher'.";
        } else {
            $result = $courseModel->create($course_name, $teacher_name, $class_id, $start_time, $end_time, $room, $course_date);
            echo $result;
        }
    }

  
    if (isset($_POST['modify_class'])) {
        $class_id = $_POST['class_id'];
        $student_name = $_POST['student_name'];

        
        echo "Nom soumis : " . htmlspecialchars($student_name) . "<br>";

        $student = $userModel->findByFullName($student_name);

        if ($student) {
            echo "Étudiant trouvé : " . $student['first_name'] . " " . $student['last_name'] . "<br>";

            if (!$userModel->hasClass($student['id'])) {
                $classModel->addStudentToClass($class_id, $student['id']);
                $success = "Élève ajouté à la classe avec succès !";
                echo $success;
            } else {
                $error = "Cet élève a déjà une classe.";
                echo $error;
            }
        } else {
            $error = "Élève non trouvé : " . htmlspecialchars($student_name);
            echo $error;
        }
    }
}
?>
