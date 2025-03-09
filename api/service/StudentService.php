<?php
require_once __DIR__ . '/../repositories/StudentRepository.php';

class StudentService {
    private $studentRepository;

    public function __construct() {
        $this->studentRepository = new StudentRepository();
    }

    private function calculateFinalGrade($midterm, $final) {
        return (0.4 * $midterm) + (0.6 * $final);
    }

    private function getPassFailStatus($final_grade) {
        return $final_grade >= 75 ? 'Pass' : 'Fail';
    }

    public function addStudent($name, $midterm, $final) {
        try {

            $final_grade = $this->calculateFinalGrade($midterm, $final);
            $status = $this->getPassFailStatus($final_grade);
            return $this->studentRepository->addStudent($name, $midterm, $final, $final_grade, $status);
        } catch (Exception $e) {
            error_log("Error adding student: " . $e->getMessage());
            return false;
        }
    }

    public function getAllStudents() {
        try{
            $students = $this->studentRepository->getAllStudents();
            foreach ($students as &$student) {
                $student['stud_final_grade'] = $this->calculateFinalGrade($student['stud_midterm_score'], $student['stud_final_score']);
                $student['stud_status'] = $this->getPassFailStatus($student['stud_final_grade']);
            }
            return $students;
        }catch (Exception $e) {
            error_log("Error fetching students: " . $e->getMessage());
            return [];
        }
    }

    public function getStudent($id) {
        try{
            $student = $this->studentRepository->getStudent($id);
            if ($student) {
                $student['stud_final_grade'] = $this->calculateFinalGrade($student['stud_midterm_score'], $student['stud_final_score']);
                $student['stud_status'] = $this->getPassFailStatus($student['stud_final_grade']);
            }
            return $student;
        } catch (Exception $e) {
            error_log("Error fetching students: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateStudent($id, $data) {
        try {
            $student = $this->studentRepository->getStudent($id);
            if (!$student) {
                throw new Exception("Student not found");
            }

            $midterm = isset($data['stud_midterm_score']) ? (float)$data['stud_midterm_score'] : (float)$student['stud_midterm_score'];
            $final = isset($data['stud_final_score']) ? (float)$data['stud_final_score'] : (float)$student['stud_final_score'];
            $final_grade = $this->calculateFinalGrade($midterm, $final);
            $status = $this->getPassFailStatus($final_grade);

            $this->studentRepository->updateStudent([
                'id' => $id,
                'midterm' => $midterm,
                'final' => $final,
                'final_grade' => $final_grade,
                'status' => $status
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error updating student: " . $e->getMessage());
            return false;
        }
    }

    public function deleteStudent($id) {
        try{
            $student = $this->studentRepository->getStudent($id);
            if (!$student) {
                throw new Exception("Student not found.");
            }
        
            return $this->studentRepository->deleteStudent($id);
        }catch (Exception $e){
            error_log("Error deleting student: " . $e->getMessage());
                return false;
        }
    }
    
}
?>
