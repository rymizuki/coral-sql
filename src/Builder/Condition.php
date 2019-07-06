<?php namespace CoralSQL\Builder;
use CoralSQL\Escape;

class Condition
{
    private $field;
    private $expr;

    /**
     * @param string|Escape\Value $field
     * @param Expression $expr
     */
    public function __construct($field, $expr)
    {
        $this->field = Escape::encode($field);
        $this->expr = $expr;
    }

    /**
     * toSQL()
     * @return string
     */
    public function toSQL(): string
    {
        return sprintf('(%s %s)', $this->field->getValue(), $this->expr->toSQL());
    }

    /**
     * getBindParams()
     * @return array
     */
    public function getBindParams(): array
    {
        return $this->expr->getBindParams();
    }
}
