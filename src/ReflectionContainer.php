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

use Closure;

use Fuel\Container\Argument\{ArgumentResolverInterface, ArgumentResolverTrait};
use Fuel\Container\Exception\ContainerException;
use Fuel\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

use function array_key_exists;
use function class_exists;
use function explode;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;
use function strpos;

/**
 * @since 2.0
 */
class ReflectionContainer implements ArgumentResolverInterface, ContainerInterface
{
	use ArgumentResolverTrait;
	use ContainerAwareTrait;

	/**
	 * @var boolean
	 */
	protected $cacheResolutions;

	/**
	 * @var array
	 */
	protected $cache = [];

	/**
	 * -----------------------------------------------------------------------------
	 * Class constructor
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function __construct(bool $cacheResolutions = false)
	{
		$this->cacheResolutions = $cacheResolutions;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function get(string $id, array $args = []): mixed
	{
		if ($this->cacheResolutions === true and array_key_exists($id, $this->cache))
		{
			return $this->cache[$id];
		}

		if ( ! $this->has($id))
		{
			throw new NotFoundException(
				sprintf('Alias (%s) is not an existing class and therefore cannot be resolved', $id)
			);
		}

		$reflector = new ReflectionClass($id);
		$construct = $reflector->getConstructor();

		if ($construct and ! $construct->isPublic())
		{
			throw new NotFoundException(
				sprintf('Alias (%s) has a non-public constructor and therefore cannot be instantiated', $id)
			);
		}

		$resolution = $construct === null
			? new $id()
			: $reflector->newInstanceArgs($this->reflectArguments($construct, $args));

		if ($this->cacheResolutions === true)
		{
			$this->cache[$id] = $resolution;
		}

		return $resolution;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function has(string $id): bool
	{
		return class_exists($id);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function call(callable $callable, array $args = [])
	{
		if (is_string($callable) and strpos($callable, '::') !== false)
		{
			$callable = explode('::', $callable);
		}

		if (is_array($callable))
		{
			if (isset($callable[0]) and is_string($callable[0]))
			{
				// if we have a definition container, try that first, otherwise, reflect
				try
				{
					$callable[0] = $this->getContainer()->get($callable[0]);
				}
				catch (ContainerException $e)
				{
					$callable[0] = $this->get($callable[0]);
				}
			}

			$reflection = new ReflectionMethod($callable[0], $callable[1]);

			if ($reflection->isStatic())
			{
				$callable[0] = null;
			}

			return $reflection->invokeArgs($callable[0], $this->reflectArguments($reflection, $args));
		}

		if (is_object($callable))
		{
			$reflection = new ReflectionMethod($callable, '__invoke');
			return $reflection->invokeArgs($callable, $this->reflectArguments($reflection, $args));
		}

		$reflection = new ReflectionFunction(Closure::fromCallable($callable));

		return $reflection->invokeArgs($this->reflectArguments($reflection, $args));
	}
}
