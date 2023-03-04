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

use Fuel\Container\Definition\{DefinitionAggregate, DefinitionInterface, DefinitionAggregateInterface};
use Fuel\Container\Exception\{NotFoundException, ContainerException};
use Fuel\Container\Inflector\{InflectorAggregate, InflectorInterface, InflectorAggregateInterface};
use Fuel\Container\ServiceProvider\{ServiceProviderAggregate,
	ServiceProviderAggregateInterface,
	ServiceProviderInterface};
use Psr\Container\ContainerInterface;

use function array_walk;
use function is_object;
use function sprintf;

/**
 * @since 2.0
 */
class Container implements DefinitionContainerInterface
{
	/**
	 * @var boolean
	 */
	protected $defaultToShared = false;

	/**
	 * @var DefinitionAggregateInterface
	 */
	protected $definitions;

	/**
	 * @var ServiceProviderAggregateInterface
	 */
	protected $providers;

	/**
	 * @var InflectorAggregateInterface
	 */
	protected $inflectors;

	/**
	 * @var ContainerInterface[]
	 */
	protected $delegates = [];

	/**
	 * @var string[]
	 */
	protected $autoTags = [];

	/**
	 * -----------------------------------------------------------------------------
	 * Class constructor
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function __construct(
		DefinitionAggregateInterface $definitions = null,
		ServiceProviderAggregateInterface $providers = null,
		InflectorAggregateInterface $inflectors = null)
	{
		$this->definitions = $definitions ?? new DefinitionAggregate();
		$this->providers   = $providers   ?? new ServiceProviderAggregate();
		$this->inflectors  = $inflectors  ?? new InflectorAggregate();

		if ($this->definitions instanceof ContainerAwareInterface)
		{
			$this->definitions->setContainer($this);
		}

		if ($this->providers instanceof ContainerAwareInterface)
		{
			$this->providers->setContainer($this);
		}

		if ($this->inflectors instanceof ContainerAwareInterface)
		{
			$this->inflectors->setContainer($this);
		}
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function add(string $id, mixed $concrete = null): DefinitionInterface
	{
		$concrete = $concrete ?? $id;

		if ($this->defaultToShared === true)
		{
			$definition = $this->addShared($id, $concrete);
		}
		else
		{
			$definition = $this->definitions->add($id, $concrete);
		}

		if (isset($this->autoTags[$id]))
		{
			foreach ($this->autoTags[$id] as $tag)
			{
				$definition->addTag($tag);
			}
		}

		return $definition;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addShared(string $id, mixed $concrete = null): DefinitionInterface
	{
		$concrete = $concrete ?? $id;

		$definition = $this->definitions->addShared($id, $concrete);

		if (isset($this->autoTags[$id]))
		{
			foreach ($this->autoTags[$id] as $tag)
			{
				$definition->addTag($tag);
			}
		}

		return $definition;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function autoTag(string $id, string $tag): void
	{
		if (isset($this->autoTags[$id]))
		{
			$this->autoTags[$id][] = $tag;
		}
		else
		{
			$this->autoTags[$id] = [$tag];
		}
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function defaultToShared(bool $shared = true): ContainerInterface
	{
		$this->defaultToShared = $shared;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function extend(string $id): DefinitionInterface
	{
		if ($this->providers->provides($id))
		{
			$this->providers->register($id);
		}

		if ($this->definitions->has($id))
		{
			return $this->definitions->getDefinition($id);
		}

		throw new NotFoundException(sprintf(
			'Unable to extend alias (%s) as it is not being managed as a definition',
			$id
		));
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addServiceProvider(ServiceProviderInterface $provider): DefinitionContainerInterface
	{
		$this->providers->add($provider);

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function get(string $id): mixed
	{
		return $this->resolve($id);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getNew(string $id): mixed
	{
		return $this->resolve($id, true);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getWith(string $id, array $params): mixed
	{
		return $this->resolve($id, false, $params);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getWithNew(string $id, array $params): mixed
	{
		return $this->resolve($id, true, $params);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function has(string $id, bool $resolved = false): bool
	{
		if ($this->definitions->has($id, $resolved))
		{
			return true;
		}

		if ($this->definitions->hasTag($id, $resolved))
		{
			return true;
		}

		if ($resolved === false and $this->providers->provides($id))
		{
			return true;
		}

		foreach ($this->delegates as $delegate)
		{
			if ($delegate->has($id))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function inflector(string $type, callable|null $callback = null): InflectorInterface
	{
		return $this->inflectors->add($type, $callback);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function delegate(ContainerInterface $container): self
	{
		$this->delegates[] = $container;

		if ($container instanceof ContainerAwareInterface)
		{
			$container->setContainer($this);
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
	protected function resolve(string $id, bool $new = false, array $params = []): mixed
	{
		if ($this->definitions->has($id))
		{
			$resolved = ($new === true) ? $this->definitions->resolveNew($id, $params) : $this->definitions->resolve($id, $params);
			return is_object($resolved) ? $this->inflectors->inflect($resolved) : $resolved;
		}

		if ($this->definitions->hasTag($id))
		{
			$arrayOf = ($new === true)
				? $this->definitions->resolveTaggedNew($id)
				: $this->definitions->resolveTagged($id);

			array_walk($arrayOf, function (&$resolved) {
				is_object($resolved) and $resolved = $this->inflectors->inflect($resolved);
			});

			return $arrayOf;
		}

		if ($this->providers->provides($id))
		{
			$this->providers->register($id);

			if (!$this->definitions->has($id) && !$this->definitions->hasTag($id))
			{
				throw new ContainerException(sprintf('Service provider lied about providing (%s) service', $id));
			}

			return $this->resolve($id, $new, $params);
		}

		foreach ($this->delegates as $delegate)
		{
			if ($delegate->has($id))
			{
				$resolved = $delegate->get($id);
				return is_object($resolved) ? $this->inflectors->inflect($resolved) : $resolved;
			}
		}

		throw new NotFoundException(sprintf('Alias (%s) is not being managed by the container or delegates', $id));
	}
}
