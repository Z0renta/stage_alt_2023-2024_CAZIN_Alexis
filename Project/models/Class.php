<?php
class ClassModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($class_name) {
        $sql = "INSERT INTO classes (class_name) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$class_name]);
    }

    public function addStudentToClass($class_id, $student_id) {
        $sql = "INSERT INTO students_classes (class_id, student_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$class_id, $student_id]);
    }

    public function getAllClasses() {
        $stmt = $this->pdo->query("SELECT * FROM classes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsByClass($class_id) {
        $stmt = $this->pdo->prepare("SELECT u.id, u.first_name, u.last_name FROM users u JOIN students_classes sc ON u.id = sc.student_id WHERE sc.class_id = ?");
        $stmt->execute([$class_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
