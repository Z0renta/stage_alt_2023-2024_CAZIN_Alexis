<?php
class CourseModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($course_name, $teacher_name, $class_id, $start_time, $end_time, $room, $course_date) {
        $sql = "SELECT COUNT(*) FROM courses WHERE class_id = ? AND course_date = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$class_id, $course_date, $start_time, $start_time, $end_time, $end_time]);

        if ($stmt->fetchColumn() > 0) {
            return "Erreur : Il existe déjà un cours à cette plage horaire pour cette classe à la date sélectionnée.";
        }

        $sql = "INSERT INTO courses (course_name, teacher_name, class_id, start_time, end_time, room, course_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$course_name, $teacher_name, $class_id, $start_time, $end_time, $room, $course_date]);

        return "Cours créé avec succès pour la date sélectionnée.";
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT c.*, cl.class_name FROM courses c JOIN classes cl ON c.class_id = cl.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteById($id) {

        $stmt = $this->pdo->prepare("DELETE FROM absences WHERE course_id = ?");
        $stmt->execute([$id]);

        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getCoursesByTeacherGrouped($teacher_id) {
        $sql = "SELECT c.course_name, GROUP_CONCAT(c.id) as course_ids 
                FROM courses c 
                JOIN users u ON c.teacher_name = u.username 
                WHERE u.id = ? 
                GROUP BY c.course_name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCoursesByTeacher($teacher_id) {
        $sql = "SELECT c.*, cl.class_name FROM courses c JOIN users u ON c.teacher_name = u.username JOIN classes cl ON c.class_id = cl.id WHERE u.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
