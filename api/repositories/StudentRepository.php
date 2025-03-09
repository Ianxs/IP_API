<?php
require_once __DIR__ . '/../config/database.php';

class StudentRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addStudent($name, $midterm, $final, $final_grade, $status) {
        $stmt = $this->db->prepare("INSERT INTO student (stud_name, stud_midterm_score, stud_final_score, stud_final_grade, stud_status) 
                                    VALUES (:name, :midterm, :final, :final_grade, :status)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':midterm', $midterm);
        $stmt->bindParam(':final', $final);
        $stmt->bindParam(':final_grade',$final_grade);
        $stmt->bindParam(':status',$status);
        return $stmt->execute();
    }

    public function getAllStudents() {
        $stmt = $this->db->query("SELECT * FROM student");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudent($id) {
        $stmt = $this->db->prepare("SELECT * FROM student WHERE stud_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateStudent($entity) {
        if (!isset($entity['midterm']) || !is_numeric($entity['midterm'])) {
            throw new Exception("Midterm grade is required and must be a number");
        }

        if (!isset($entity['final']) || !is_numeric($entity['final'])) {
            throw new Exception("Final grade is required and must be a number");
        }

        if (!isset($entity['id']) || !is_numeric($entity['id'])) {
            throw new Exception("Student ID is required and must be a number");
        }

        $query = "UPDATE student SET stud_midterm_score = :midterm, stud_final_score = :final, stud_final_grade = :final_grade, stud_status = :status WHERE stud_id = :id";
        $params = [
            ':midterm' => $entity['midterm'],
            ':final' => $entity['final'],
            ':final_grade' => $entity['final_grade'],
            ':id' => $entity['id'],
            ':status' => $entity['status']
        ];

        $stmt = $this->db->prepare($query);
        $result = $stmt->execute($params);

        if (!$result) {
            error_log("Update failed: " . implode(" | ", $stmt->errorInfo()));
            throw new Exception("Database update failed");
        }
    }

    
    public function deleteStudent($id) {
        $query = "DELETE FROM student WHERE stud_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    
    
}
?>
