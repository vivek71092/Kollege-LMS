<?php
// /classes/Attendance.php

class Attendance {
    
    private $db; // Stores the PDO connection object

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Marks attendance for multiple students at once.
     * Uses INSERT...ON DUPLICATE KEY UPDATE to be efficient.
     *
     * @param array $records An array of attendance records. Each record is an assoc array:
     * [
     * 'student_id' => 1,
     * 'subject_id' => 1,
     * 'date'       => '2025-10-23',
     * 'status'     => 'present',
     * 'teacher_id' => 2,
     * 'remarks'    => ''
     * ]
     * @return bool True on success.
     */
    public function markBatch($records) {
        if (empty($records)) {
            return false;
        }

        $sql = "INSERT INTO Attendance (student_id, subject_id, date, status, teacher_id, remarks, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE status = VALUES(status), remarks = VALUES(remarks), teacher_id = VALUES(teacher_id)";
        
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare($sql);
            foreach ($records as $record) {
                $stmt->execute([
                    $record['student_id'],
                    $record['subject_id'],
                    $record['date'],
                    $record['status'],
                    $record['teacher_id'],
                    $record['remarks'] ?? ''
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            log_error($e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }

    /**
     * Gets all attendance records for a student in a specific subject.
     * @param int $student_id The student's ID.
     * @param int $subject_id The subject's ID.
     * @return array List of attendance records.
     */
    public function getForStudent($student_id, $subject_id) {
        $stmt = $this->db->prepare("SELECT * FROM Attendance WHERE student_id = ? AND subject_id = ? ORDER BY date DESC");
        $stmt->execute([$student_id, $subject_id]);
        return $stmt->fetchAll();
    }

    /**
     * Gets all attendance records for a subject on a specific date.
     * @param int $subject_id The subject's ID.
     * @param string $date The date (Y-m-d).
     * @return array List of attendance records.
     */
    public function getForSubjectByDate($subject_id, $date) {
        $sql = "SELECT a.*, u.first_name, u.last_name 
                FROM Attendance a 
                JOIN Users u ON a.student_id = u.id
                WHERE a.subject_id = ? AND a.date = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$subject_id, $date]);
        return $stmt->fetchAll();
    }
}
?>