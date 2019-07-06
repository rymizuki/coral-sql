<?php namespace CoralSQL\Escape;

interface Value
{
    /**
     * @return string
     */
    public function getValue(): string;
}