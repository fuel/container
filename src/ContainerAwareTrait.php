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

namespace Fuel\Container;

use BadMethodCallException;
use Fuel\Container\Exception\ContainerException;

/**
 * @since 2.0
 */
trait ContainerAwareTrait
{
    /**
     * @var ?DefinitionContainerInterface
     */
    protected $container;

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function setContainer(DefinitionContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;

        if ($this instanceof ContainerAwareInterface)
        {
            return $this;
        }

        throw new BadMethodCallException(sprintf(
            'Attempt to use (%s) while not implementing (%s)',
            ContainerAwareTrait::class,
            ContainerAwareInterface::class
        ));
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function getContainer(): DefinitionContainerInterface
    {
        if ($this->container instanceof DefinitionContainerInterface)
        {
            return $this->container;
        }

        throw new ContainerException('No container implementation has been set.');
    }
}
