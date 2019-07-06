<?php namespace CoralSQL;
use CoralSQL\Escape\Unescaped;

use CoralSQL\Builder\Table;
use CoralSQL\Builder\Columns;
use CoralSQL\Builder\Orders;
use CoralSQL\Builder\Conditions;
use CoralSQL\Builder\Join;

class Builder
{
    private $indent = "    ";

    private $table;
    private $columns;
    private $conditions;
    private $orders;
    private $joins = [];

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

    public function column($field, $alias = null): self
    {
        $this->columns->add($field, $alias);
        return $this;
    }

    public function from($table, $alias = null): self
    {
        $this->table = ($table instanceof Table) ? $table : new Table($table);
        if ($alias) {
            $this->table->as($alias);
        }
        return $this;
    }

    public function leftJoin(...$args): self
    {
        $this->joins[] = new Join('left', ...$args);
        return $this;
    }

    public function where(...$args): self
    {
        $this->conditions->and(...$args);
        return $this;
    }

    public function orderBy($field, $direction): self
    {
        $this->orders->add($field, $direction);
        return $this;
    }

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

    public function getBindParams(): array
    {
        return $this->conditions->getBindParams();
    }

    public static function unescape(string $value): Unescaped
    {
        return new Unescaped($value);
    }
}
