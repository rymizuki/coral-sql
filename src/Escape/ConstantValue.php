<?php namespace CoralSQL\Escape;

final class ConstantValue
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value; // FIXME
    }
}
