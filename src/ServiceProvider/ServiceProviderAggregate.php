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

namespace Fuel\Container\ServiceProvider;

use Generator;
use Fuel\Container\Exception\ContainerException;
use Fuel\Container\{ContainerAwareInterface, ContainerAwareTrait};

use function in_array;
use function sprintf;

/**
 * @since 2.0
 */
class ServiceProviderAggregate implements ServiceProviderAggregateInterface
{
	use ContainerAwareTrait;

	/**
	 * @var ServiceProviderInterface[]
	 */
	protected $providers = [];

	/**
	 * @var array
	 */
	protected $registered = [];

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function add(ServiceProviderInterface $provider): ServiceProviderAggregateInterface
	{
		if (in_array($provider, $this->providers, true))
		{
			return $this;
		}

		if ($provider instanceof ContainerAwareInterface)
		{
			$provider->setContainer($this->getContainer());
		}

		if ($provider instanceof BootableServiceProviderInterface)
		{
			$provider->boot();
		}

		$this->providers[] = $provider;

		return $this;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function provides(string $service): bool
	{
		foreach ($this->getIterator() as $provider)
		{
			if ($provider->provides($service))
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
	public function getIterator(): Generator
	{
		yield from $this->providers;
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function register(string $service): void
	{
		if ($this->provides($service) === false)
		{
			throw new ContainerException(
				sprintf('(%s) is not provided by a service provider', $service)
			);
		}

		foreach ($this->getIterator() as $provider)
		{
			if (in_array($provider->getIdentifier(), $this->registered, true))
			{
				continue;
			}

			if ($provider->provides($service))
			{
				$provider->register();
				$this->registered[] = $provider->getIdentifier();
			}
		}
	}
}
