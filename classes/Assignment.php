<?php
// /classes/Assignment.php

class Assignment {
    
    private $db; // Stores the PDO connection object

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Creates a new assignment.
     * @param array $data Associative array of assignment data.
     * @return string The ID of the new assignment.
     */
    public function create($data) {
        $sql = "INSERT INTO Assignments (subject_id, title, description, instructions, due_date, max_marks, created_by, status, created_at) 
                VALUES (:subject_id, :title, :desc, :instr, :due, :max, :creator, 'published', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':subject_id' => $data['subject_id'],
            ':title'      => $data['title'],
            ':desc'       => $data['description'] ?? null,
            ':instr'      => $data['instructions'] ?? null,
            ':due'        => $data['due_date'],
            ':max'        => $data['max_marks'],
            ':creator'    => $data['created_by'] // Teacher's user ID
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Gets a single assignment by its ID.
     * @param int $id The assignment ID.
     * @return mixed Assignment data.
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Assignments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Creates a new student submission.
     * @param array $data Associative array of submission data.
     * @return string The ID of the new submission.
     */
    public function submit($data) {
        $sql = "INSERT INTO Submissions (assignment_id, student_id, submission_date, file_path, status) 
                VALUES (:assignment_id, :student_id, NOW(), :file_path, 'submitted')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':assignment_id' => $data['assignment_id'],
            ':student_id'    => $data['student_id'],
            ':file_path'     => $data['file_path']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Gets all submissions for a single assignment.
     * @param int $assignment_id The assignment ID.
     * @return array List of submissions.
     */
    public function getSubmissionsForAssignment($assignment_id) {
        $sql = "SELECT s.*, u.first_name, u.last_name 
                FROM Submissions s 
                JOIN Users u ON s.student_id = u.id 
                WHERE s.assignment_id = ? 
                ORDER BY s.submission_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$assignment_id]);
        return $stmt->fetchAll();
    }

    /**
     * Grades a single submission.
     * @param int $submission_id The ID of the submission.
     * @param int $marks The marks awarded.
     * @param string $feedback Feedback from the teacher.
     * @param int $teacher_id The ID of the teacher grading.
     * @return bool True on success.
     */
    public function grade($submission_id, $marks, $feedback, $teacher_id) {
        $sql = "UPDATE Submissions SET 
                marks_obtained = ?, 
                feedback = ?, 
                graded_date = NOW(), 
                graded_by = ?, 
                status = 'graded' 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$marks, $feedback, $teacher_id, $submission_id]);
    }
}
?>