<?php
require_once __DIR__ . '/../config/Database.php';

class Student {
    private $connection;
    private $table ="student";

    public function __construct(){
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function addStudent($name, $midterm, $final) { 
        $query = "INSERT INTO " . $this->table . " (stud_name, stud_midterm_score, stud_final_score) VALUES (:name, :midterm, :final)";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":midterm", $midterm);
        $stmt->bindParam(":final", $final);
        return $stmt->execute();
    }
    
    public function getAllStudents() {
        $query = "SELECT stud_id, stud_name, stud_midterm_score, stud_final_score, (0.4 * stud_midterm_score + 0.6 * stud_final_score) AS stud_final_grade, 
                 CASE WHEN (0.4 * stud_midterm_score + 0.6 * stud_final_score) >= 75 THEN 'Pass' ELSE 'Fail' END AS status 
                 FROM " . $this->table;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudent($id) {
        $query = "SELECT stud_id, stud_name, stud_midterm_score, stud_final_score, 
                         (0.4 * stud_midterm_score + 0.6 * stud_final_score) AS stud_final_grade, 
                         CASE WHEN (0.4 * stud_midterm_score + 0.6 * stud_final_score) >= 75 THEN 'Pass' ELSE 'Fail' END AS status 
                  FROM " . $this->table . " WHERE stud_id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateStudent($id, $midterm, $final) {
        $query = "UPDATE " . $this->table . " SET stud_midterm_score = :midterm, stud_final_score = :final WHERE stud_id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":midterm", $midterm);
        $stmt->bindParam(":final", $final);
        return $stmt->execute();
    }

    public function deleteStudent($id) {
        $query = "DELETE FROM " . $this->table . " WHERE stud_id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
  
}