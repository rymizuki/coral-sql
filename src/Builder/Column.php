<?php namespace CoralSQL\Builder;
use CoralSQL\Escape;

class Column
{
    protected $value;
    protected $alias;

    /**
     * @param string|Escape\Value $field
     * @param string|Escape\Value|null $alias
     */
    public function __construct($field, $alias = null)
    {
        $this->field = Escape::encode($field);
        if ($alias !== null) {
            $this->alias = Escape::encode($alias);
        }
    }

    /**
     * toSQL()
     * @return string;
     */
    public function toSQL(): string
    {
        $field = $this->field->getValue(); 
        $alias = $this->alias === null ? '' : sprintf(' AS %s', $this->alias->getValue());
        return $field . $alias;
    }
}
