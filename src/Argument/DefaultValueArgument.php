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

/**
 * @since 2.0
 */
class DefaultValueArgument extends ResolvableArgument implements DefaultValueInterface
{
	/**
	 */
	protected mixed $defaultValue;

	/**
	 * -----------------------------------------------------------------------------
	 * Class constructor
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function __construct(string $value, mixed $defaultValue = null)
	{
		$this->defaultValue = $defaultValue;
		parent::__construct($value);
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getDefaultValue(): mixed
	{
		return $this->defaultValue;
	}
}
