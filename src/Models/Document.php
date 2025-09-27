<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Database;

class Document
{
    public int $id;
    public int $opportunity_id;
    public int $file_id;
    public string $label;
    public string $created_at;

    // Related objects
    public ?File $file = null;

    /**
     * Find document by ID
     */
    public static function findById(int $id): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM documents WHERE id = ?',
            [$id]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Create document from array data
     */
    public static function fromArray(array $data): self
    {
        $document = new self();
        $document->id = (int) $data['id'];
        $document->opportunity_id = (int) $data['opportunity_id'];
        $document->file_id = (int) $data['file_id'];
        $document->label = $data['label'];
        $document->created_at = $data['created_at'];

        return $document;
    }

    /**
     * Load document with related file
     */
    public function loadFile(): void
    {
        $this->file = File::findById($this->file_id);
    }

    /**
     * Get documents for opportunity
     */
    public static function getByOpportunityId(int $opportunityId): array
    {
        $data = Database::query(
            'SELECT d.*, f.original_name, f.mime_type, f.size, f.path 
             FROM documents d 
             JOIN files f ON d.file_id = f.id 
             WHERE d.opportunity_id = ? 
             ORDER BY d.created_at DESC',
            [$opportunityId]
        );

        return array_map([self::class, 'fromArray'], $data);
    }

    /**
     * Create new document
     */
    public static function create(int $opportunityId, int $fileId, string $label): self
    {
        Database::execute(
            'INSERT INTO documents (opportunity_id, file_id, label) VALUES (?, ?, ?)',
            [$opportunityId, $fileId, $label]
        );

        $id = (int) Database::lastInsertId();
        return self::findById($id);
    }

    /**
     * Delete document
     */
    public function delete(): bool
    {
        Database::execute('DELETE FROM documents WHERE id = ?', [$this->id]);
        return true;
    }
}