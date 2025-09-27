<?php

declare(strict_types=1);

namespace GovTribe\Models;

use GovTribe\Utils\Database;
use DateTime;
use DateTimeInterface;

/**
 * Base model class with common functionality
 */
abstract class BaseModel
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = ['id', 'created_at', 'updated_at'];
    protected array $casts = [];
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get attribute value
     */
    public function __get(string $key)
    {
        if (array_key_exists($key, $this->attributes)) {
            $value = $this->attributes[$key];
            
            // Apply casts
            if (isset($this->casts[$key])) {
                return $this->castAttribute($key, $value);
            }
            
            return $value;
        }

        return null;
    }

    /**
     * Set attribute value
     */
    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Check if attribute exists
     */
    public function __isset(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get all attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set multiple attributes
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || empty($this->fillable)) {
                if (!in_array($key, $this->guarded)) {
                    $this->attributes[$key] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Cast attribute to specified type
     */
    protected function castAttribute(string $key, $value)
    {
        if ($value === null) {
            return null;
        }

        $cast = $this->casts[$key];

        switch ($cast) {
            case 'int':
            case 'integer':
                return (int) $value;

            case 'float':
            case 'double':
                return (float) $value;

            case 'bool':
            case 'boolean':
                return (bool) $value;

            case 'string':
                return (string) $value;

            case 'array':
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;

            case 'date':
            case 'datetime':
                if ($value instanceof DateTimeInterface) {
                    return $value;
                }
                
                if (is_string($value)) {
                    try {
                        return new DateTime($value);
                    } catch (\Exception $e) {
                        return null;
                    }
                }
                
                return $value;

            default:
                return $value;
        }
    }

    /**
     * Get fillable attributes for database operations
     */
    public function getFillableAttributes(): array
    {
        $attributes = [];
        
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $this->fillable) || empty($this->fillable)) {
                if (!in_array($key, $this->guarded)) {
                    $attributes[$key] = $this->prepareValueForDatabase($value);
                }
            }
        }

        return $attributes;
    }

    /**
     * Prepare value for database storage
     */
    protected function prepareValueForDatabase($value)
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return $value;
    }

    /**
     * Save model to database
     */
    public function save(): bool
    {
        $attributes = $this->getFillableAttributes();
        
        if (empty($this->attributes[$this->primaryKey])) {
            // Insert new record
            $attributes['created_at'] = date('Y-m-d H:i:s');
            $attributes['updated_at'] = date('Y-m-d H:i:s');
            
            $columns = implode(', ', array_keys($attributes));
            $placeholders = ':' . implode(', :', array_keys($attributes));
            
            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            
            Database::execute($sql, $attributes);
            
            $this->attributes[$this->primaryKey] = (int) Database::lastInsertId();
            
            return true;
        } else {
            // Update existing record
            $attributes['updated_at'] = date('Y-m-d H:i:s');
            
            $setParts = [];
            foreach (array_keys($attributes) as $key) {
                $setParts[] = "{$key} = :{$key}";
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . 
                   " WHERE {$this->primaryKey} = :{$this->primaryKey}";
            
            $attributes[$this->primaryKey] = $this->attributes[$this->primaryKey];
            
            return Database::execute($sql, $attributes) > 0;
        }
    }

    /**
     * Delete model from database
     */
    public function delete(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return Database::execute($sql, ['id' => $this->attributes[$this->primaryKey]]) > 0;
    }

    /**
     * Refresh model from database
     */
    public function refresh(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $data = Database::fetchOne($sql, ['id' => $this->attributes[$this->primaryKey]]);
        
        if ($data) {
            $this->attributes = $data;
            return true;
        }

        return false;
    }

    /**
     * Check if model exists in database
     */
    public function exists(): bool
    {
        return !empty($this->attributes[$this->primaryKey]);
    }

    /**
     * Get primary key value
     */
    public function getKey(): ?int
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    /**
     * Set primary key value
     */
    public function setKey($value): void
    {
        $this->attributes[$this->primaryKey] = $value;
    }

    /**
     * Convert model to array
     */
    public function toArray(): array
    {
        $array = [];
        
        foreach ($this->attributes as $key => $value) {
            $array[$key] = $this->$key;
        }
        
        return $array;
    }

    /**
     * Convert model to JSON
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Create new model instance
     */
    public static function create(array $attributes): self
    {
        $model = new static();
        $model->fill($attributes);
        $model->save();
        return $model;
    }

    /**
     * Find model by ID
     */
    public static function find(int $id): ?self
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} WHERE {$model->primaryKey} = :id";
        $data = Database::fetchOne($sql, ['id' => $id]);
        
        if ($data) {
            $model->attributes = $data;
            return $model;
        }
        
        return null;
    }

    /**
     * Find model by ID or throw exception
     */
    public static function findOrFail(int $id): self
    {
        $model = static::find($id);
        
        if (!$model) {
            throw new \RuntimeException("Model not found with ID: {$id}");
        }
        
        return $model;
    }

    /**
     * Get all models
     */
    public static function all(array $columns = ['*']): array
    {
        $model = new static();
        $columnList = $columns === ['*'] ? '*' : implode(', ', $columns);
        $sql = "SELECT {$columnList} FROM {$model->table}";
        
        $results = Database::fetchAll($sql);
        $models = [];
        
        foreach ($results as $data) {
            $model = new static();
            $model->attributes = $data;
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * Get models with conditions
     */
    public static function where(string $column, $operator, $value = null): QueryBuilder
    {
        $model = new static();
        return new QueryBuilder($model->table, get_called_class());
    }

    /**
     * Get first model matching conditions
     */
    public static function first(): ?self
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} LIMIT 1";
        $data = Database::fetchOne($sql);
        
        if ($data) {
            $model->attributes = $data;
            return $model;
        }
        
        return null;
    }
}