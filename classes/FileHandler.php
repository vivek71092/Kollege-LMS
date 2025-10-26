<?php
// /classes/FileHandler.php

class FileHandler {

    private $base_upload_dir;

    /**
     * @param string $base_upload_dir The root directory for all uploads (e.g., '../public/uploads/').
     */
    public function __construct($base_upload_dir = __DIR__ . '/../public/uploads/') {
        $this->base_upload_dir = rtrim($base_upload_dir, '/') . '/';
    }

    /**
     * Handles a file upload.
     *
     * @param array $file The $_FILES['name'] array.
     * @param string $sub_folder The destination sub-folder (e.g., 'notes', 'submissions').
     * @param array $allowed_types An array of allowed MIME types.
     * @param int $max_size The maximum file size in bytes.
     * @return array Associative array: ['success' => bool, 'path' => string (db path), 'error' => string]
     */
    public function upload($file, $sub_folder, $allowed_types = [], $max_size = 10485760) { // 10MB
        
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload error: ' . $file['error']);
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('File is too large. Max size is ' . ($max_size / 1024 / 1024) . 'MB.');
            }

            if (!empty($allowed_types)) {
                $file_type = mime_content_type($file['tmp_name']);
                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception('Invalid file type. (' . $file_type . ')');
                }
            }

            // Create a secure, unique filename
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safe_filename = uniqid() . bin2hex(random_bytes(8)) . '.' . $file_extension;
            
            // Create destination directory
            $destination_dir = $this->base_upload_dir . $sub_folder;
            if (!is_dir($destination_dir)) {
                if (!mkdir($destination_dir, 0755, true) && !is_dir($destination_dir)) {
                    throw new Exception('Failed to create upload directory.');
                }
            }
            
            $full_upload_path = $destination_dir . '/' . $safe_filename;
            
            // Move the file
            if (!move_uploaded_file($file['tmp_name'], $full_upload_path)) {
                throw new Exception('Failed to move uploaded file.');
            }

            // Return the *relative* path for the database
            $db_path = 'public/uploads/' . $sub_folder . '/' . $safe_filename;
            
            return ['success' => true, 'path' => $db_path];

        } catch (Exception $e) {
            log_error($e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Deletes a file from the server.
     * @param string $db_path The relative path as stored in the database.
     * @return bool True on success.
     */
    public function delete($db_path) {
        $full_path = __DIR__ . '/../' . $db_path;
        
        if (file_exists($full_path) && is_writable($full_path)) {
            return unlink($full_path);
        }
        return false;
    }
}
?>