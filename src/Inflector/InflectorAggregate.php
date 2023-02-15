<?php declare(strict_types=1);

/**
 * The Fuel PHP Framework is a fast, simple and flexible development framework
 *
 * @package    fuel
 * @version    2.0.0
 * @author     FlexCoders Ltd, Fuel The PHP Framework Team
 * @license    MIT License
 * @copyright  2021 Phil Bennett <philipobenito@gmail.com>
 * @copyright  2023 FlexCoders Ltd, The Fuel PHP Framework Team
 * @link       https://fuelphp.org
 */

namespace Fuel\Container\Inflector;

use Generator;
use Fuel\Container\ContainerAwareTrait;

/**
 * @since 2.0
 */
class InflectorAggregate implements InflectorAggregateInterface
{
    use ContainerAwareTrait;

    /**
     * @var Inflector[]
     */
    protected $inflectors = [];

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function add(string $type, callable $callback = null): Inflector
    {
        $inflector = new Inflector($type, $callback);
        $this->inflectors[] = $inflector;

        return $inflector;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function inflect($object)
    {
        foreach ($this->getIterator() as $inflector)
        {
            $type = $inflector->getType();

            if ($object instanceof $type)
            {
                $inflector->setContainer($this->getContainer());
                $inflector->inflect($object);
            }
        }

        return $object;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function getIterator(): Generator
    {
        yield from $this->inflectors;
    }
}
