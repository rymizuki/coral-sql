<?php namespace CoralSQL\Builder;

class Columns
{
    private $options = [];
    private $rows = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * add()
     * @param string|CoralSQL\Escape\Value $field
     * @param string|CoralSQL\Escape\Value|null $alias
     * @return self
     */
    public function add($field, $alias): self
    {
        $this->rows[] = new Column($field, $alias);
        return $this;
    }

    /**
     * toSQL()
     * @return string
     */
    public function toSQL(): string
    {
        $indent = $this->options['indent'];

        return count($this->rows) === 0 ? "${indent}*" : join(",\n", array_map(function ($column) use ($indent) {
            return $indent . $column->toSQL();
        }, $this->rows));
    }
}