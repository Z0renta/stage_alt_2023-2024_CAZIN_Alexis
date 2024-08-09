<?php
class GradeModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getStudentsByClass($class_id) {
        $stmt = $this->pdo->prepare("SELECT u.id, u.first_name, u.last_name FROM users u JOIN students_classes sc ON u.id = sc.student_id WHERE sc.class_id = ?");
        $stmt->execute([$class_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignGrade($student_id, $course_id, $grade) {
        $stmt = $this->pdo->prepare("INSERT INTO notes (student_id, course_id, grade) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE grade = VALUES(grade)");
        $stmt->execute([$student_id, $course_id, $grade]);
    }
}
?>
