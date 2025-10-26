<?php
// /classes/Course.php

class Course {
    
    private $db; // Stores the PDO connection object

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    // --- Course (Program) Methods ---

    /**
     * Gets a single course (program) by its ID.
     * @param int $id The course ID.
     * @return mixed Course data.
     */
    public function getCourse($id) {
        $stmt = $this->db->prepare("SELECT * FROM Courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Gets all courses (programs).
     * @return array List of all courses.
     */
    public function getAllCourses() {
        $sql = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) AS teacher_name 
                FROM Courses c 
                LEFT JOIN Users u ON c.teacher_id = u.id 
                ORDER BY c.course_name";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Creates a new course (program).
     * @param array $data Associative array of course data.
     * @return string The ID of the new course.
     */
    public function createCourse($data) {
        $sql = "INSERT INTO Courses (course_code, course_name, description, semester, teacher_id, status, created_at) 
                VALUES (:code, :name, :desc, :sem, :teacher, :status, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':code'    => $data['course_code'],
            ':name'    => $data['course_name'],
            ':desc'    => $data['description'] ?? null,
            ':sem'     => $data['semester'],
            ':teacher' => $data['teacher_id'] ?? null,
            ':status'  => $data['status']
        ]);
        return $this->db->lastInsertId();
    }

    // --- Subject Methods ---

    /**
     * Gets a single subject by its ID.
     * @param int $id The subject ID.
     * @return mixed Subject data.
     */
    public function getSubject($id) {
        $stmt = $this->db->prepare("SELECT * FROM Subjects WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Gets all subjects for a specific course.
     * @param int $course_id The ID of the parent course.
     * @return array List of subjects.
     */
    public function getSubjectsByCourse($course_id) {
        $stmt = $this->db->prepare("SELECT * FROM Subjects WHERE course_id = ? ORDER BY semester, subject_name");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Creates a new subject.
     * @param array $data Associative array of subject data.
     * @return string The ID of the new subject.
     */
    public function createSubject($data) {
        $sql = "INSERT INTO Subjects (course_id, subject_code, subject_name, semester, credits, status, created_at) 
                VALUES (:course_id, :code, :name, :sem, :credits, :status, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':course_id' => $data['course_id'],
            ':code'      => $data['subject_code'],
            ':name'      => $data['subject_name'],
            ':sem'       => $data['semester'],
            ':credits'   => $data['credits'] ?? 3,
            ':status'    => $data['status']
        ]);
        return $this->db->lastInsertId();
    }
}
?>