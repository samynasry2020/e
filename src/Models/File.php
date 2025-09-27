<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Database;

class File
{
    public int $id;
    public string $path;
    public string $original_name;
    public string $mime_type;
    public int $size;
    public string $sha256;
    public ?string $source_url;
    public string $created_at;

    /**
     * Find file by ID
     */
    public static function findById(int $id): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM files WHERE id = ?',
            [$id]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Find file by SHA256 hash
     */
    public static function findByHash(string $sha256): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM files WHERE sha256 = ?',
            [$sha256]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Create file from array data
     */
    public static function fromArray(array $data): self
    {
        $file = new self();
        $file->id = (int) $data['id'];
        $file->path = $data['path'];
        $file->original_name = $data['original_name'];
        $file->mime_type = $data['mime_type'];
        $file->size = (int) $data['size'];
        $file->sha256 = $data['sha256'];
        $file->source_url = $data['source_url'];
        $file->created_at = $data['created_at'];

        return $file;
    }

    /**
     * Create new file record
     */
    public static function create(array $data): self
    {
        Database::execute(
            'INSERT INTO files (path, original_name, mime_type, size, sha256, source_url) VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['path'],
                $data['original_name'],
                $data['mime_type'],
                $data['size'],
                $data['sha256'],
                $data['source_url'] ?? null
            ]
        );

        $id = (int) Database::lastInsertId();
        return self::findById($id);
    }

    /**
     * Check if file exists on disk
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * Get file contents
     */
    public function getContents(): ?string
    {
        if (!$this->exists()) {
            return null;
        }

        return file_get_contents($this->path);
    }

    /**
     * Get file size in human readable format
     */
    public function getHumanSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get file extension
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return strpos($this->mime_type, 'image/') === 0;
    }

    /**
     * Check if file is a PDF
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if file is a document
     */
    public function isDocument(): bool
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ];

        return in_array($this->mime_type, $documentTypes);
    }

    /**
     * Delete file from disk and database
     */
    public function delete(): bool
    {
        $deleted = true;

        // Delete from disk if it exists
        if ($this->exists()) {
            $deleted = unlink($this->path);
        }

        // Delete from database
        Database::execute('DELETE FROM files WHERE id = ?', [$this->id]);

        return $deleted;
    }

    /**
     * Get files by age (for cleanup)
     */
    public static function getOldFiles(int $daysOld = 30): array
    {
        $data = Database::query(
            'SELECT * FROM files WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)',
            [$daysOld]
        );

        return array_map([self::class, 'fromArray'], $data);
    }

    /**
     * Clean up orphaned files
     */
    public static function cleanupOrphanedFiles(): int
    {
        // Find files that are not referenced by any documents
        $orphanedFiles = Database::query(
            'SELECT f.id FROM files f 
             LEFT JOIN documents d ON f.id = d.file_id 
             WHERE d.file_id IS NULL'
        );

        $deleted = 0;
        foreach ($orphanedFiles as $fileData) {
            $file = self::findById($fileData['id']);
            if ($file && $file->delete()) {
                $deleted++;
            }
        }

        return $deleted;
    }
}