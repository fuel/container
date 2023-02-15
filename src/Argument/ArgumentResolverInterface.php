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

namespace Fuel\Container\Argument;

use Fuel\Container\ContainerAwareInterface;
use ReflectionFunctionAbstract;

/**
 * @since 2.0
 */
interface ArgumentResolverInterface extends ContainerAwareInterface
{
    public function resolveArguments(array $arguments, array $params = null): array;
    public function reflectArguments(ReflectionFunctionAbstract $method, array $args = []): array;
}
