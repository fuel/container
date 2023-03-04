<?php declare(strict_types=1);

/**
 * The Fuel PHP Framework is a fast, simple and flexible development framework
 *
 * @package    fuel
 * @version    2.0.0
 * @author     FlexCoders Ltd, Fuel The PHP Framework Team
 * @license    MIT License
 * @copyright  2023 FlexCoders Ltd, The Fuel PHP Framework Team
 * @copyright  2021 Phil Bennett <philipobenito@gmail.com>
 * @link       https://fuelphp.org
 */

namespace Fuel\Container\Definition;

use Generator;
use Fuel\Container\ContainerAwareTrait;
use Fuel\Container\Exception\NotFoundException;

use array_filter;
use sprintf;

/**
 * @since 2.0
 */
class DefinitionAggregate implements DefinitionAggregateInterface
{
	use ContainerAwareTrait;

	/**
	 * @var DefinitionInterface[]
	 */
	protected $definitions = [];

	/**
	 * -----------------------------------------------------------------------------
	 * Class constructor
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function __construct(array $definitions = [])
	{
		$this->definitions = array_filter($definitions, static function ($definition) {
			return ($definition instanceof DefinitionInterface);
		});
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function add(string $id, mixed $definition): DefinitionInterface
	{
		$definition = new Definition($id, $definition);

		$this->definitions[] = $definition->setAlias($id);

		return $definition;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function addShared(string $id, mixed $definition): DefinitionInterface
	{
		$definition = $this->add($id, $definition);

		return $definition->setShared(true);
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
		foreach ($this->getIterator() as $definition)
		{
			if ($id === $definition->getAlias())
			{
				if ( ! $resolved or $definition->isResolved())
				{
					return true;
				}
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
	public function hasTag(string $tag, bool $resolved = false): bool
	{
		foreach ($this->getIterator() as $definition)
		{
			if ($definition->hasTag($tag))
			{
				if ( ! $resolved or $definition->isResolved())
				{
					return true;
				}
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
	public function getDefinition(string $id): DefinitionInterface
	{
		foreach ($this->getIterator() as $definition)
		{
			if ($id === $definition->getAlias())
			{
				return $definition->setContainer($this->getContainer());
			}
		}

		throw new NotFoundException(sprintf('Alias (%s) is not being handled as a definition.', $id));
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function resolve(string $id, array $params = []): mixed
	{
		return $this->getDefinition($id)->resolve($params);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function resolveNew(string $id, array $params = []): mixed
	{
		return $this->getDefinition($id)->resolveNew($params);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function resolveTagged(string $tag): array
	{
		$arrayOf = [];

		foreach ($this->getIterator() as $definition)
		{
			if ($definition->hasTag($tag))
			{
				$arrayOf[] = $definition->setContainer($this->getContainer())->resolve();
			}
		}

		return $arrayOf;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function resolveTaggedNew(string $tag): array
	{
		$arrayOf = [];

		foreach ($this->getIterator() as $definition)
		{
			if ($definition->hasTag($tag))
			{
				$arrayOf[] = $definition->setContainer($this->getContainer())->resolveNew();
			}
		}

		return $arrayOf;
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
		yield from $this->definitions;
	}
}
