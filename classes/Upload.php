<?php
class Upload
{
    private $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    private $maxSize = 2 * 1024 * 1024; // 2MB

    public function handle($file, $destination)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload failed.'];
        }

        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'message' => 'File too large. Maximum size is 2MB.'];
        }

        $mime = mime_content_type($file['tmp_name']);
        if (!isset($this->allowed[$mime])) {
            return ['success' => false, 'message' => 'Invalid file type. Use JPG, PNG, GIF, or WebP.'];
        }

        $ext = $this->allowed[$mime];
        $filename = uniqid('', true) . '.' . $ext;
        $fullPath = $destination . $filename;

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            return ['success' => false, 'message' => 'Could not save the file.'];
        }

        return ['success' => true, 'path' => $fullPath, 'filename' => $filename];
    }
}
