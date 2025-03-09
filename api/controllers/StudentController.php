<?php
require_once __DIR__ . '/../service/StudentService.php';

class StudentController {
    private $studentService;

    public function __construct() {
        $this->studentService = new StudentService();
    }

    public function addStudent() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['stud_midterm_score']) || !is_numeric($input['stud_midterm_score'])) {
            http_response_code(400);
            echo json_encode(["message" => "Midterm score is required and must be a positive number."]);
            return;
        }

        if (!isset($input['stud_final_score']) || !is_numeric($input['stud_final_score'])) {
            http_response_code(400);
            echo json_encode(["message" => "Final score is required and must be a positive number."]);
            return;
        }

        $name = $input['stud_name'];
        $midterm = (float)$input['stud_midterm_score'];
        $final = (float)$input['stud_final_score'];

        $result = $this->studentService->addStudent($name, $midterm, $final);

        if ($result) {
            http_response_code(201);
            echo json_encode(["message" => "Student added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to add student"]);
        }
    }

    public function getAllStudents() {
        $students = $this->studentService->getAllStudents();
        echo json_encode($students);
    }

    public function getStudent($id) {
        $student = $this->studentService->getStudent($id);
        if ($student) {
            echo json_encode($student);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Student not found"]);
        }
    }

    public function updateStudent($data, $id) {
        if (!is_numeric($id)) {
            echo json_encode(["error" => "Student ID must be a number"]);
            return;
        }
        
        $result = $this->studentService->updateStudent($id, $data);
        echo json_encode($result ? ["message" => "Student updated successfully"] : ["error" => "Failed to update student"]);
    }
    
    public function deleteStudent($params) {
        if (!isset($params[0])) {
            echo json_encode(["message" => "Student ID is required"]);
            return;
        }
    
        $id = intval($params[0]);
    
        try {
            $result = $this->studentService->deleteStudent($id);
            if ($result) {
                echo json_encode(["message" => "Student deleted successfully"]);
            } else {
                echo json_encode(["message" => "Failed to delete student"]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
?>
