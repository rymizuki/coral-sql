<?php namespace CoralSQL\Builder;

use CoralSQL\Escape;

class Table
{
    protected $name;
    protected $alias;


    /**
     * Table constructor.
     * new Table($name)
     * new Table($name, $alias)
     *
     * @param $name
     * @param null $alias
     */
    public function __construct($name, $alias = null)
    {
        $this->name = Escape::encode($name);
        if ($alias !== null) {
            $this->as($alias);
        }
    }

    /**
     * as($alias)
     *
     * @param $alias
     * @return Table
     */
    public function as($alias): self
    {
        $this->alias = Escape::encode($alias);
        return $this;
    }

    /**
     * @return string
     */
    public function toSQL(): string
    {
        $name = $this->name->getValue();
        $alias = $this->alias ? sprintf(' AS %s', $this->alias->getValue()) : '';
        return $name . $alias;
    }
}
