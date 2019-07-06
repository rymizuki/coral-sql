<?php namespace CoralSQL\Escape;

final class Escaped implements Value
{
    private $value;

    /**
     * Escaped constructor.
     * @param string $value
     */
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