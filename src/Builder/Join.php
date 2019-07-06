<?php namespace CoralSQL\Builder;

use CoralSQL\Builder\Table;

class Join
{
    private $direction;
    private $table;
    private $condition;

    public function __construct($direction, ...$args)
    {
        $this->direction = $this->createDirection($direction);

        if (count($args) == 2) {
            $this->table = $this->createTable($args[0]);
            $this->condition = $args[1];
        }
        if (count($args) == 3) {
            $this->table = $this->createTable($args[0]);
            $this->table->as($args[1]);
            $this->condition = $args[2];
        }
    }

    /**
     * toSQL()
     * @return string
     */
    public function toSQL(): string
    {
        return join(" ", [
            sprintf('%s JOIN', $this->direction),
            $this->table->toSQL(),
            'ON',
            $this->condition
        ]);
    }

    private function createDirection($direction): string
    {
        switch ($direction) {
            case 'left': return 'LEFT';
            case 'right': return 'RIGHT';
            case 'inner': return 'INNER';
            default: return '';
        }
    }

    private function createTable($name): Table
    {
        return $name instanceof Table ? $name : new Table($name);
    }
}