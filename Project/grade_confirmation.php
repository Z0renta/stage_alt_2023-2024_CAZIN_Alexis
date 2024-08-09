<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $course_name = $_POST['course_name']; 
    $max_grade = $_POST['max_grade'];
    $grades = $_POST['grades'];

    foreach ($grades as $student_id => $grade) {
        if ($grade <= $max_grade) {
            $stmt = $pdo->prepare("SELECT id FROM notes WHERE student_id = ? AND course_id IN (SELECT id FROM courses WHERE course_name = ? AND class_id = ?)");
            $stmt->execute([$student_id, $course_name, $class_id]);
            $existing_note = $stmt->fetch();

            if ($existing_note) {
                $stmt = $pdo->prepare("UPDATE notes SET grade = ? WHERE id = ?");
                $stmt->execute([$grade, $existing_note['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO notes (student_id, course_id, grade) VALUES (?, (SELECT id FROM courses WHERE course_name = ? AND class_id = ? LIMIT 1), ?)");
                $stmt->execute([$student_id, $course_name, $class_id, $grade]);
            }
        }
    }

    header('Location: teacher_dashboard.php');
    exit();
}
?>
