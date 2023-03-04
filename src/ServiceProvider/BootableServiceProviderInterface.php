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

/**
 * @since 2.0
 */
interface BootableServiceProviderInterface extends ServiceProviderInterface
{
	/**
	 * Method will be invoked on registration of a service provider implementing
	 * this interface. Provides ability for eager loading of Service Providers.
	 *
	 * @return void
	 */
	public function boot(): void;
}
