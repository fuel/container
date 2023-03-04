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

use Fuel\Container\ContainerAwareTrait;

use function get_class;

/**
 * @since 2.0
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface
{
	use ContainerAwareTrait;

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getIdentifier(): string
	{
		return $this->identifier ?? get_class($this);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function setIdentifier(string $id): ServiceProviderInterface
	{
		$this->identifier = $id;
		return $this;
	}
}
