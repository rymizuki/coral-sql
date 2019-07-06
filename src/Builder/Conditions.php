<?php namespace CoralSQL\Builder;

use CoralSQL\Escape;
use CoralSQL\Builder\Condition;
use CoralSQL\Builder\Condition\Expression;

class Conditions
{
    private $rows = [];

    /**
     * hasFields()
     *
     * @return bool
     */
    public function hasFields(): bool
    {
        return count($this->rows) > 0;
    }

    /**
     * add($field, $value)
     * add($field, $values)
     * add($field, $operator, $value)
     * add($condition)
     * add($conditions)
     *
     * @param mixed ...$args
     * @return Conditions
     */
    public function and(...$args): self
    {
        return $this->add('and', ...$args);
    }

    /**
     * or($field, $value)
     * or($field, $values)
     * or($field, $operator, $value)
     * or($condition)
     * or($conditions)
     *
     * @param mixed ...$args
     * @return Conditions
     */
    public function or(...$args): self
    {
        return $this->add('or', ...$args);
    }

    /**
     * add($conjunction, $field, $value)
     * add($conjunction, $operator, $value)
     * add($conjunction, $instance)
     *
     * @param $conjunction - 'and' | 'or'
     * @param mixed ...$args
     * @return Conditions
     */
    public function add($conjunction, ...$args): self
    {
        $this->rows[] = [
            'conjunction' => $this->createConjunction($conjunction),
            'condition' => $this->createCondition($args),
        ];
        return $this;
    }

    /**
     * toSQL()
     *
     * @return string
     */
    public function toSQL(): string
    {
        $output = '';
        foreach ($this->rows as $index => $row) {
            $conjunction = ($index === 0) ? '' : (' ' . $row['conjunction'] . ' ');
            $condition = $row['condition'] instanceof Conditions ? sprintf('(%s)', $row['condition']->toSQL()) : $row['condition']->toSQL();

            $output .=  $conjunction . $condition;
        }
        return $output;
    }

    /**
     * getBindParams()
     *
     * @return array
     */
    public function getBindParams(): array
    {
        $params = [];
        foreach ($this->rows as $row) {
            $params = array_merge($params, $row['condition']->getBindParams());
        }
        return $params;
    }

    private function createConjunction(string $conjunction)
    {
        switch ($conjunction) {
            case 'and': return 'AND';
            case 'or': return 'OR';
            default:
                // ARIENAI
                throw new Exception();
        }
    }

    private function createCondition($args)
    {
        if (count($args) === 1 && ($args[0] instanceof Conditions || $args[0] instanceof Condition)) {
            return $args[0];
        }
        if (count($args) === 2) {
            $operator = is_array($args[1]) ? 'in' : '=';
            return new Condition($args[0], new Expression($operator, $args[1]));
        } 
        if (count($args) === 3) {
            return new Condition($args[0], new Expression($args[1], $args[2]));
        }
        // ARIENAI
        throw new Exception();
    }
}