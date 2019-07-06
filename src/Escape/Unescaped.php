<?php namespace CoralSQL\Escape;

final class Unescaped implements Value
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * getValue
     * @return string
     */
    public function getValue(): string
    {
        return $this->value; // FIXME
    }
}