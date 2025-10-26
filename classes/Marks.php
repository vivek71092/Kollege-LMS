<?php
// /classes/Marks.php

class Marks {
    
    private $db; // Stores the PDO connection object

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Updates the central marks record for a student in a subject.
     * @param int $student_id The student's ID.
     * @param int $subject_id The subject's ID.
     * @param array $data Associative array of marks data (e.g., ['midterm_marks' => 80]).
     * @param int $teacher_id The ID of the teacher updating the marks.
     * @return bool True on success.
     */
    public function update($student_id, $subject_id, $data, $teacher_id) {
        // 1. Fetch existing marks to recalculate total and grade
        $existing = $this->getForStudent($student_id, $subject_id);
        
        // Merge new data with existing
        $marks = [
            'assignment_marks' => $data['assignment_marks'] ?? $existing['assignment_marks'] ?? null,
            'midterm_marks'    => $data['midterm_marks'] ?? $existing['midterm_marks'] ?? null,
            'final_marks'      => $data['final_marks'] ?? $existing['final_marks'] ?? null,
        ];
        
        // 2. Recalculate
        $total = ($marks['assignment_marks'] ?? 0) + ($marks['midterm_marks'] ?? 0) + ($marks['final_marks'] ?? 0);
        $grade = $this->calculateGrade($total);

        // 3. Use INSERT...ON DUPLICATE KEY UPDATE
        $sql = "INSERT INTO Marks (student_id, subject_id, assignment_marks, midterm_marks, final_marks, total_marks, grade, teacher_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                assignment_marks = VALUES(assignment_marks),
                midterm_marks = VALUES(midterm_marks),
                final_marks = VALUES(final_marks),
                total_marks = VALUES(total_marks),
                grade = VALUES(grade),
                teacher_id = VALUES(teacher_id),
                updated_at = NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $student_id,
            $subject_id,
            $marks['assignment_marks'],
            $marks['midterm_marks'],
            $marks['final_marks'],
            $total,
            $grade,
            $teacher_id
        ]);
    }

    /**
     * Gets the marks record for a single student in a single subject.
     * @param int $student_id The student's ID.
     * @param int $subject_id The subject's ID.
     * @return mixed Marks data.
     */
    public function getForStudent($student_id, $subject_id) {
        $stmt = $this->db->prepare("SELECT * FROM Marks WHERE student_id = ? AND subject_id = ?");
        $stmt->execute([$student_id, $subject_id]);
        return $stmt->fetch();
    }

    /**
     * Calculates a letter grade from a total. (Simple placeholder)
     * @param int $total The total marks.
     * @return string The letter grade.
     */
    private function calculateGrade($total) {
        if ($total >= 90) return 'A+';
        if ($total >= 85) return 'A';
        if ($total >= 80) return 'A-';
        if ($total >= 75) return 'B+';
        if ($total >= 70) return 'B';
        if ($total >= 65) return 'B-';
        if ($total >= 60) return 'C+';
        if ($total >= 50) return 'C';
        if ($total >= 40) return 'D';
        return 'F';
    }
}
?>