<?php namespace CoralSQL\Builder;
use CoralSQL\Escape;

class Condition
{
    private $field;
    private $expr;

    /**
     * Condition constructor.
     *
     * @param $field
     * @param $expr
     */
    public function __construct($field, $expr)
    {
        $this->field = Escape::encode($field);
        $this->expr = $expr;
    }

    /**
     * toSQL()
     *
     * @return string
     */
    public function toSQL(): string
    {
        return sprintf('(%s %s)', $this->field->getValue(), $this->expr->toSQL());
    }

    /**
     * getBindParams()
     *
     * @return array
     */
    public function getBindParams(): array
    {
        return $this->expr->getBindParams();
    }
}
