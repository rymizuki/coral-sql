<?php
namespace CoralSQL\Builder\Condition;

use CoralSQL\Escape\ConstantValue;

final class ConstantExpression implements ExpressionInterface
{
    public const IS_NULL = 'IS NULL';
    public const IS_NOT_NULL = 'IS NOT NULL';

    private $value;

    public function __construct(ConstantValue $value)
    {
        $this->value = $value;
    }

    public function toSQL(): string
    {
        return $this->createExpression();
    }

    public function getBindParams(): array
    {
        return [];
    }

    private function createExpression()
    {
        switch ($this->value->getValue()) {
            case self::IS_NULL :
                return self::IS_NULL;
            case self::IS_NOT_NULL :
                return self::IS_NOT_NULL;
        }
    }
}
