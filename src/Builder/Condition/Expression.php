<?php namespace CoralSQL\Builder\Condition;

use CoralSQL\DataType;
use CoralSQL\Escape\Unescaped;

class Expression implements ExpressionInterface
{
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';
    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
    public const BETWEEN = 'BETWEEN';
    public const REGEXP = 'REGEXP';

    private $operator;
    private $value;
    private $params;


    /**
     * Expression constructor.
     *
     * @param string $operator - '=', 'in', 'not in', 'between', etc...
     * @param $value
     */
    public function __construct(string $operator, $value)
    {
        $this->operator = $this->createOperator($operator);
        $this->value = $value;
    }

    public function toSQL(): string
    {
        $this->params = [];

        if ($this->operator === self::IN || $this->operator === self::NOT_IN) {
            $values = [];
            foreach ($this->value as $value) {
                $values[] = $this->handleBindParam($value);
            }
            return sprintf('%s (%s)', $this->operator, join(',', $values));
        } else if ($this->operator === self::BETWEEN) {
            return sprintf(
                '%s %s AND %s',
                $this->operator,
                $this->handleBindParam($this->value[0]),
                $this->handleBindParam($this->value[1])
            );
        }

        return $this->operator . ' ' . $this->handleBindParam($this->value);
}

    public function getBindParams(): array
    {
        return $this->params;
    }

    private function handleBindParam($value): string
    {
        if ($value instanceof Unescaped) {
            return $value->getValue();
        }

        $this->addBindParam($value);
        return '?';
    }

    private function addBindParam($value): self
    {
        $this->params[] = [
            'value' => $value,
            'dataType' => DataType::getDataType($value),
        ];
        return $this;
    }

    private function createOperator($op)
    {
        switch ($op) {
            case 'in' : return self::IN;
            case 'not in' : return self::NOT_IN;
            case 'like' : return self::LIKE;
            case 'not like': return self::NOT_LIKE;
            case 'between': return self::BETWEEN;
            case 'regexp': return self::REGEXP;
            default: return $op;
        }
    }
}
