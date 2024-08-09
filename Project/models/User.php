<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function create($username, $password, $first_name, $last_name, $role)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $first_name, $last_name, $role]);
    }

    public function findByFullName($full_name)
    {
        $parts = explode(" ", $full_name);
        if (count($parts) < 2) {
            return false; 
        }

        $first_name = $parts[0];
        $last_name = $parts[1];

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE first_name = ? AND last_name = ?");
        $stmt->execute([$first_name, $last_name]);
        return $stmt->fetch();
    }

    public function findByName($name)
    {
        return $this->findByFullName($name);
    }

    public function hasClass($student_id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM students_classes WHERE student_id = ?");
        $stmt->execute([$student_id]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
