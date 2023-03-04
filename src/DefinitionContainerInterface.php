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

use Fuel\Container\Definition\DefinitionInterface;
use Fuel\Container\Inflector\InflectorInterface;
use Fuel\Container\ServiceProvider\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * @since 2.0.0
 */
interface DefinitionContainerInterface extends ContainerInterface
{
	public function add(string $id, mixed $concrete = null): DefinitionInterface;
	public function addServiceProvider(ServiceProviderInterface $provider): self;
	public function addShared(string $id, mixed $concrete = null): DefinitionInterface;
	public function extend(string $id): DefinitionInterface;
	public function getNew(string $id): mixed;
	public function getWith(string $id, array $params): mixed;
	public function getWithNew(string $id, array $params): mixed;
	public function inflector(string $type, callable $callback = null): InflectorInterface;
}
