<?php namespace CoralSQL\Builder\Condition;

use CoralSQL\DataType;

class Expression
{
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';
    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
    public const IS_NULL = 'IS NULL';
    public const IS_NOT_NULL = 'IS NOT NULL';
    public const BETWEEN = 'BETWEEN';

    private $operator;
    private $value;
    private $params;

    /**
     * @param string $operator
     * @param mixed $value
     */
    public function __construct(string $operator, $value)
    {
        $this->operator = $this->createOperator($operator);
        $this->value = $value;
    }

    /**
     * toSQL()
     * @return string
     */
    public function toSQL(): string
    {
        $this->params = [];

        if ($this->operator === self::IN || $this->operator === self::NOT_IN) {
            $placeholders = [];
            foreach ($this->value as $value) {
                $this->addBindParam($value);
                $placeholders[] = '?';
            }
            return sprintf('%s (%s)', $this->operator, join(',', $placeholders));
        } else if ($this->operator === self::BETWEEN) {
            $this->addBindParam($this->value[0]);
            $this->addBindParam($this->value[1]);
            return sprintf('%s ? AND ?', $this->operator);
        } else {
            $this->addBindParam($this->value);
            return $this->operator . ' ?';
        }
    }

    /**
     * getBindParams()
     * @return array
     */
    public function getBindParams(): array
    {
        return $this->params;
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
            case 'is null': return self::IS_NULL;
            case 'is not null': return self::IS_NOT_NULL;
            case 'between': return self::BETWEEN;
            default: return $op;
        }
    }
}