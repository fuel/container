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

namespace Fuel\Container\Definition;

use Fuel\Container\Argument\{
	ArgumentResolverInterface,
	ArgumentResolverTrait,
	ArgumentInterface,
	LiteralArgumentInterface
};
use Fuel\Container\ContainerAwareTrait;
use Fuel\Container\Exception\{NotFoundException, ContainerException};
use Psr\Container\ContainerInterface;
use ReflectionClass;

use is_callable;
use is_string;
use is_object;
use class_exists;
use call_user_func_array;
/**
 * @since 2.0
 */
class Definition implements ArgumentResolverInterface, DefinitionInterface
{
	use ArgumentResolverTrait;
	use ContainerAwareTrait;

	/**
	 * @var string
	 */
	protected $alias;

	/**
	 * @var mixed
	 */
	protected $concrete;

	/**
	 * @var boolean
	 */
	protected $shared = false;

	/**
	 * @var array
	 */
	protected $tags = [];

	/**
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * @var array
	 */
	protected $methods = [];

	/**
	 * @var mixed
	 */
	protected $resolved;

	/**
	 * -----------------------------------------------------------------------------
	 * Class constructor
	 * -----------------------------------------------------------------------------
	 *
	 * @param string     $id
	 * @param mixed|null $concrete
	 *
	 * @since 2.0.0
	 */
	public function __construct(string $id, $concrete = null)
	{
		$concrete = $concrete ?? $id;
		$this->alias    = $id;
		$this->concrete = $concrete;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addTag(string $tag): DefinitionInterface
	{
		$this->tags[$tag] = true;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function hasTag(string $tag): bool
	{
		return isset($this->tags[$tag]);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function setAlias(string $id): DefinitionInterface
	{
		$this->alias = $id;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getAlias(): string
	{
		return $this->alias;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function setShared(bool $shared = true): DefinitionInterface
	{
		$this->shared = $shared;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function isShared(): bool
	{
		return $this->shared;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function isResolved(): bool
	{
		return ($this->resolved !== null);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getConcrete(): mixed
	{
		return $this->concrete;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function setConcrete($concrete): DefinitionInterface
	{
		$this->concrete = $concrete;
		$this->resolved = null;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addArgument($arg): DefinitionInterface
	{
		$this->arguments[] = $arg;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addArguments(array $args): DefinitionInterface
	{
		foreach ($args as $arg)
		{
			$this->addArgument($arg);
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
	public function addMethodCall(string $method, array $args = []): DefinitionInterface
	{
		$this->methods[] = [
			'method'    => $method,
			'arguments' => $args
		];

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addMethodCalls(array $methods = []): DefinitionInterface
	{
		foreach ($methods as $method => $args)
		{
			$this->addMethodCall($method, $args);
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
	public function resolve(array $params = []): mixed
	{
		if ($this->resolved !== null and $this->isShared())
		{
			if ( ! empty($params))
			{
				throw new ContainerException(sprintf('You can not pass new arguments to an already instantiated class (%s)!', $this->alias));
			}

			return $this->resolved;
		}

		return $this->resolveNew($params);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function resolveNew(array $params = []): mixed
	{
		$concrete = $this->concrete;

		if (is_callable($concrete))
		{
			$concrete = $this->resolveCallable($concrete, $params);
		}

		if ($concrete instanceof LiteralArgumentInterface)
		{
			$this->resolved = $concrete->getValue();
			return $concrete->getValue();
		}

		if ($concrete instanceof ArgumentInterface)
		{
			$concrete = $concrete->getValue();
		}

		if (is_string($concrete) and class_exists($concrete))
		{
			$concrete = $this->resolveClass($concrete, $params);
		}

		if (is_object($concrete))
		{
			$concrete = $this->invokeMethods($concrete, $params);
		}

		try
		{
			$container = $this->getContainer();
		}
		catch (ContainerException $e)
		{
			$container = null;
		}

		// if we still have a string, try to pull it from the container
		// this allows for `alias -> alias -> ... -> concrete
		if (is_string($concrete) and $container instanceof ContainerInterface and $container->has($concrete))
		{
			$concrete = $container->get($concrete);
		}

		$this->resolved = $concrete;
		return $concrete;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @param callable $concrete
	 * @return mixed
	 *
	 * @since 2.0.0
	 */
	protected function resolveCallable(callable $concrete, array $params = null)
	{
		$resolved = $this->resolveArguments($this->arguments, $params);
		return call_user_func_array($concrete, $resolved);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	protected function resolveClass(string $concrete, array $params = null): object
	{
		$reflection = new ReflectionClass($concrete);

		// check if the constructor is from a different class
		if ($reflection->getConstructor() and $reflection->getConstructor()->class !== $concrete)
		{
			try
			{
				$parent = $this->container->extend($reflection->getConstructor()->class);
			}
			catch (NotFoundException $e)
			{
				throw new NotFoundException(sprintf('Class "%s" extends "%s" which defines its constructor, but that class is not being managed by the container or delegates', $concrete, $reflection->getConstructor()->class));
			}

			$resolved = $parent->resolveArguments($parent->arguments, $params);
		}
		else
		{
			$resolved = $this->resolveArguments($this->arguments, $params);
		}

		return $reflection->newInstanceArgs($resolved);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	protected function invokeMethods(object $instance, array $params = null): object
	{
		foreach ($this->methods as $method)
		{
			$args = $this->resolveArguments($method['arguments'], $params);
			$callable = [$instance, $method['method']];
			call_user_func_array($callable, $args);
		}

		return $instance;
	}
}
