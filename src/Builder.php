<?php
namespace CoralSQL;

use CoralSQL\Escape\Unescaped;
use CoralSQL\Builder\Table;
use CoralSQL\Builder\Columns;
use CoralSQL\Builder\Orders;
use CoralSQL\Builder\Conditions;
use CoralSQL\Builder\Join;

/**
 * Class Builder
 * @package CoralSQL
 */
class Builder
{
    private $indent = "    ";

    private $table;
    private $columns;
    private $conditions;
    private $having;
    private $groups;
    private $orders;
    private $joins = [];
    private $limit;
    private $offset;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->columns = new Columns([
            'indent' => $this->indent,
        ]);
        $this->orders = new Orders([
            'indent' => $this->indent,
        ]);
        $this->conditions = new Conditions();
        $this->having = new Conditions();
        $this->groups = [];
    }

    /**
     * column($field)
     * column($field, $alias)
     *
     * @param $field
     * @param null $alias
     * @return Builder
     */
    public function column($field, $alias = null): self
    {
        $this->columns->add($field, $alias);
        return $this;
    }

    /**
     * from($name)
     * from($name, $alias)
     * from($table)
     * from($table, $alias)
     *
     * @param $table
     * @param null $alias
     * @return Builder
     */
    public function from($table, $alias = null): self
    {
        $this->table = ($table instanceof Table) ? $table : new Table($table);
        if ($alias) {
            $this->table->as($alias);
        }
        return $this;
    }

    /**
     * leftJoin($table_name, $condition)
     * leftJoin($table_name, $alias, $condition)
     * leftJoin($table, $condition)
     * leftJoin($table, $alias, $condition)
     *
     * @param mixed ...$args
     * @return Builder
     */
    public function leftJoin(...$args): self
    {
        $this->joins[] = new Join('left', ...$args);
        return $this;
    }

    /**
     * where($field, $value)
     * where($field, $values)
     * where($conditions)
     *
     * @param mixed ...$args
     * @return Builder
     */
    public function where(...$args): self
    {
        $this->conditions->and(...$args);
        return $this;
    }

    /**
     * having($filed, $value)
     * having($filed, $values)
     * having($conditions)
     *
     * @param mixed ...$args
     * @return Builder
     */
    public function having(...$args): self
    {
        $this->having->and(...$args);
        return $this;
    }

    /**
     * groupBy($column)
     * groupBy([$column, $column])
     *
     * @param mixed
     * @return Builder
     */
    public function groupBy($columns): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $this->groups = array_merge($this->groups, $columns);
        return $this;
    }

    /**
     * orderBy($field, 'desc')
     * orderBy($field, 'asc')
     *
     * @param $field
     * @param $direction
     * @return Builder
     */
    public function orderBy($field, $direction): self
    {
        $this->orders->add($field, $direction);
        return $this;
    }

    /**
     * limit(1)
     *
     * @param integer $value
     * @return self
     */
    public function limit(int $value): self
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * offset(1)
     *
     * @param integer $value
     * @return self
     */
    public function offset(int $value): self
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function toSQL(): string
    {
        $indent = $this->indent;

        $sections = array_filter([
            'SELECT',
            $this->columns->toSQL(),
            'FROM',
            $indent . $this->table->toSQL(),
            join("\n", array_map(function ($join) {
                return $join->toSQL();
            }, $this->joins)),
            $this->conditions->hasFields() ? sprintf("WHERE\n${indent}%s", $this->conditions->toSQL()) : null,
            $this->having->hasFields() ? sprintf("HAVING\n${indent}%s", $this->having->toSQL()) : null,
            $this->getGroupBy(),
            $this->orders->toSQL(),
            isset($this->limit) ? sprintf("LIMIT %s", $this->limit) : null,
            isset($this->offset) ? sprintf("OFFSET %s", $this->offset) : null,
        ], function ($row) {
            return $row;
        });

        return join("\n", $sections);
    }

    /**
     * @return array
     */
    public function getBindParams(): array
    {
        return array_merge(
            $this->conditions->getBindParams(),
            $this->having->getBindParams()
        );
    }

    /**
     * @param string $value
     * @return Unescaped
     */
    public static function unescape(string $value): Unescaped
    {
        return new Unescaped($value);
    }

    private function getGroupBy(): ?string
    {
        if (empty($this->groups)) {
            return null;
        }
        $indent = $this->indent;
        return sprintf(
            "GROUP BY\n${indent}%s",
            join(', ', array_map(function ($column) {
                return "`${column}`";
            }, $this->groups))
        );
    }
}
