<?php namespace CoralSQL\Builder;

use CoralSQL\Escape;

class Table
{
    protected $name;
    protected $alias;

    /**
     * @param string|CoralSQL\Escape\EscapeValue $name
     * @param string|CoralSQL\Escape\EscapeValue|null $alias = null
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
     * @param string|CoralSQL\Escape\EscapeValue $alias
     * @return self
     */
    public function as($alias): self
    {
        $this->alias = Escape::encode($alias);
        return $this;
    }

    /**
     * toSQL()
     * @return string
     */
    public function toSQL(): string
    {
        $name = $this->name->getValue();
        $alias = $this->alias ? sprintf(' AS %s', $this->alias->getValue()) : '';
        return $name . $alias;
    }
}
