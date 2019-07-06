<?php namespace CoralSQL\Escape;
use CoralSQL\Builder;

class Builded implements Value {
    private $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getValue(): string
    {
        return sprintf("(%s)", preg_replace("/\n/", ' ', $this->builder->toSQL()));
    }

    public function getBindParams(): array
    {
        return $this->builder->getBindParams();
    }
}