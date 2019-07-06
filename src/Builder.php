<?php namespace CoralSQL;
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
    private $orders;
    private $joins = [];

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
            $this->conditions->hasFields() ? sprintf("WHERE\n${indent}%s", $this->conditions->toSQL()) : null,
            join("\n", array_map(function ($join) {
                return $join->toSQL();
            }, $this->joins)),
            $this->orders->toSQL(),
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
        return $this->conditions->getBindParams();
    }

    /**
     * @param string $value
     * @return Unescaped
     */
    public static function unescape(string $value): Unescaped
    {
        return new Unescaped($value);
    }
}
