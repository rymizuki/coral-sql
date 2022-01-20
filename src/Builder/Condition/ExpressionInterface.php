<?php namespace CoralSQL\Builder\Condition;

interface ExpressionInterface
{
    /**
     * toSQL()
     *
     * @return string
     */
    public function toSQL(): string;

    /**
     * getBindParams()
     *
     * @return array
     */
    public function getBindParams(): array;
}
