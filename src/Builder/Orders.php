<?php namespace CoralSQL\Builder;

final class Orders
{
    private $orders;
    private $options;

    public function __construct(array $options)
    {
        $this->orders = [];
        $this->options = $options;
    }

    /**
     * add($field, $direction)
     * @param string|Escape\Value $field
     * @param string $direction
     * @return self
     */
    public function add(string $field, string $direction)
    {
        $this->orders[] = new Order($field, $direction);
        return $this;
    }

    /**
     * toSQL()
     * @return self
     */
    public function toSQL(): ?string
    {
        if (count($this->orders) === 0) {
            return null;
        }

        $indent = $this->options['indent'];

        $orders = join(",\n", array_map(function ($order) use ($indent) {
            return $indent . $order->toSQL();
        }, $this->orders));

        return "ORDER BY\n".$orders;
    }
}
