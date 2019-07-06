<?php namespace CoralSQL\Builder;
use CoralSQL\Escape;

class Order
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * @param string|Escape\Value $field
     * @param string $direction
     */
    public function __construct($field, $direction)
    {
        $this->field = Escape::encode($field);
        $this->direction = $direction;
    }

    /**
     * toSQL()
     * @return string
     */
    public function toSQL(): string
    {
       return sprintf('%s %s', $this->field->getValue(), $this->direction);
    }
}
