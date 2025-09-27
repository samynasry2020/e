<?php

declare(strict_types=1);

namespace GovTribe\Models;

use GovTribe\Utils\Database;

/**
 * Query builder for database operations
 */
class QueryBuilder
{
    private string $table;
    private string $modelClass;
    private array $wheres = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $select = ['*'];
    private array $params = [];

    public function __construct(string $table, string $modelClass)
    {
        $this->table = $table;
        $this->modelClass = $modelClass;
    }

    /**
     * Set columns to select
     */
    public function select(array $columns): self
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * Add WHERE condition
     */
    public function where(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'where',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'and'
        ];

        return $this;
    }

    /**
     * Add OR WHERE condition
     */
    public function orWhere(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'where',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'or'
        ];

        return $this;
    }

    /**
     * Add WHERE IN condition
     */
    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = [
            'type' => 'whereIn',
            'column' => $column,
            'values' => $values,
            'boolean' => 'and'
        ];

        return $this;
    }

    /**
     * Add WHERE NOT IN condition
     */
    public function whereNotIn(string $column, array $values): self
    {
        $this->wheres[] = [
            'type' => 'whereNotIn',
            'column' => $column,
            'values' => $values,
            'boolean' => 'and'
        ];

        return $this;
    }

    /**
     * Add WHERE NULL condition
     */
    public function whereNull(string $column): self
    {
        $this->wheres[] = [
            'type' => 'whereNull',
            'column' => $column,
            'boolean' => 'and'
        ];

        return $this;
    }

    /**
     * Add WHERE NOT NULL condition
     */
    public function whereNotNull(string $column): self
    {
        $this->wheres[] = [
            'type' => 'whereNotNull',
            'column' => $column,
            'boolean' => 'and'
        ];

        return $this;
    }

    /**
     * Add ORDER BY clause
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC'
        ];

        return $this;
    }

    /**
     * Set LIMIT clause
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set OFFSET clause
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set LIMIT and OFFSET (for pagination)
     */
    public function skip(int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * Set LIMIT and OFFSET (for pagination)
     */
    public function take(int $limit): self
    {
        return $this->limit($limit);
    }

    /**
     * Get first result
     */
    public function first(): ?BaseModel
    {
        $this->limit = 1;
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Get all results
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $results = Database::fetchAll($sql, $this->params);
        
        $models = [];
        foreach ($results as $data) {
            $model = new $this->modelClass();
            $model->attributes = $data;
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * Get count of results
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            [$whereClause, $params] = $this->buildWhereClause();
            $sql .= " {$whereClause}";
            $this->params = array_merge($this->params, $params);
        }
        
        $result = Database::fetchOne($sql, $this->params);
        return (int) $result['count'];
    }

    /**
     * Check if any results exist
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Delete matching records
     */
    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            [$whereClause, $params] = $this->buildWhereClause();
            $sql .= " {$whereClause}";
            $this->params = array_merge($this->params, $params);
        }
        
        return Database::execute($sql, $this->params);
    }

    /**
     * Update matching records
     */
    public function update(array $data): int
    {
        $setParts = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $key = "update_{$column}";
            $setParts[] = "{$column} = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        
        if (!empty($this->wheres)) {
            [$whereClause, $whereParams] = $this->buildWhereClause();
            $sql .= " {$whereClause}";
            $params = array_merge($params, $whereParams);
        }
        
        return Database::execute($sql, $params);
    }

    /**
     * Build SELECT query
     */
    private function buildSelectQuery(): string
    {
        $selectClause = implode(', ', $this->select);
        $sql = "SELECT {$selectClause} FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            [$whereClause, $params] = $this->buildWhereClause();
            $sql .= " {$whereClause}";
            $this->params = array_merge($this->params, $params);
        }
        
        if (!empty($this->orderBy)) {
            $orderClause = $this->buildOrderByClause();
            $sql .= " {$orderClause}";
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }

    /**
     * Build WHERE clause
     */
    private function buildWhereClause(): array
    {
        if (empty($this->wheres)) {
            return ['', []];
        }

        $conditions = [];
        $params = [];

        foreach ($this->wheres as $index => $where) {
            $boolean = $index > 0 ? strtoupper($where['boolean']) : '';
            
            switch ($where['type']) {
                case 'where':
                    $key = "where_{$where['column']}_{$index}";
                    $conditions[] = "{$boolean} {$where['column']} {$where['operator']} :{$key}";
                    $params[$key] = $where['value'];
                    break;
                    
                case 'whereIn':
                    $placeholders = [];
                    foreach ($where['values'] as $i => $value) {
                        $key = "where_in_{$where['column']}_{$index}_{$i}";
                        $placeholders[] = ":{$key}";
                        $params[$key] = $value;
                    }
                    $conditions[] = "{$boolean} {$where['column']} IN (" . implode(',', $placeholders) . ")";
                    break;
                    
                case 'whereNotIn':
                    $placeholders = [];
                    foreach ($where['values'] as $i => $value) {
                        $key = "where_not_in_{$where['column']}_{$index}_{$i}";
                        $placeholders[] = ":{$key}";
                        $params[$key] = $value;
                    }
                    $conditions[] = "{$boolean} {$where['column']} NOT IN (" . implode(',', $placeholders) . ")";
                    break;
                    
                case 'whereNull':
                    $conditions[] = "{$boolean} {$where['column']} IS NULL";
                    break;
                    
                case 'whereNotNull':
                    $conditions[] = "{$boolean} {$where['column']} IS NOT NULL";
                    break;
            }
        }

        return ['WHERE ' . implode(' ', $conditions), $params];
    }

    /**
     * Build ORDER BY clause
     */
    private function buildOrderByClause(): string
    {
        $clauses = [];
        
        foreach ($this->orderBy as $order) {
            $clauses[] = "{$order['column']} {$order['direction']}";
        }
        
        return 'ORDER BY ' . implode(', ', $clauses);
    }
}