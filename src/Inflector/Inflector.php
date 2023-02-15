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

use Fuel\Container\Argument\ArgumentResolverInterface;
use Fuel\Container\Argument\ArgumentResolverTrait;
use Fuel\Container\ContainerAwareTrait;

/**
 * @since 2.0
 */
class Inflector implements ArgumentResolverInterface, InflectorInterface
{
    use ArgumentResolverTrait;
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var callable|null
     */
    protected $callback;

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * -----------------------------------------------------------------------------
     * Class constructor
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function __construct(string $type, callable|null $callback = null)
    {
        $this->type = $type;
        $this->callback = $callback;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function invokeMethod(string $name, array $args): InflectorInterface
    {
        $this->methods[$name] = $args;

        return $this;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function invokeMethods(array $methods): InflectorInterface
    {
        foreach ($methods as $name => $args)
        {
            $this->invokeMethod($name, $args);
        }

        return $this;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function setProperty(string $property, $value): InflectorInterface
    {
        $this->properties[$property] = $this->resolveArguments([$value])[0];

        return $this;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function setProperties(array $properties): InflectorInterface
    {
        foreach ($properties as $property => $value)
        {
            $this->setProperty($property, $value);
        }

        return $this;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function inflect(object $object): void
    {
        $properties = $this->resolveArguments(array_values($this->properties));
        $properties = array_combine(array_keys($this->properties), $properties);

        // array_combine() can technically return false
        foreach ($properties ?: [] as $property => $value)
        {
            $object->{$property} = $value;
        }

        foreach ($this->methods as $method => $args)
        {
            $args = $this->resolveArguments($args);
            $callable = [$object, $method];
            call_user_func_array($callable, $args);
        }

        if ($this->callback !== null)
        {
            call_user_func($this->callback, $object);
        }
    }
}
