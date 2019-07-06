<?php namespace CoralSQL\Builder;

final class Orders
{
    private $orders;
    private $options;

    /**
     * Orders constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->orders = [];
        $this->options = $options;
    }

    /**
     * add($field, 'asc')
     * add($field, 'desc')
     *
     * @param $field
     * @param string $direction
     * @return Orders
     */
    public function add($field, string $direction): Orders
    {
        $this->orders[] = new Order($field, $direction);
        return $this;
    }

    /**
     * toSQL()
     *
     * @return string
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
