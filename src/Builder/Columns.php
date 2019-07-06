<?php namespace CoralSQL\Builder;

class Columns
{
    private $options = [];
    private $rows = [];

    /**
     * Columns constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }


    /**
     * add($field)
     * add($field, $alias)
     *
     * @param $field
     * @param $alias
     * @return Columns
     */
    public function add($field, $alias): self
    {
        $this->rows[] = new Column($field, $alias);
        return $this;
    }

    /**
     * toSQL()
     *
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