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

use InvalidArgumentException;

use function gettype;
use function is_callable;
use function is_object;

/**
 * @since 2.0
 */
class LiteralArgument implements LiteralArgumentInterface
{
	public const TYPE_ARRAY    = 'array';
	public const TYPE_BOOL     = 'boolean';
	public const TYPE_BOOLEAN  = 'boolean';
	public const TYPE_CALLABLE = 'callable';
	public const TYPE_DOUBLE   = 'double';
	public const TYPE_FLOAT    = 'double';
	public const TYPE_INT      = 'integer';
	public const TYPE_INTEGER  = 'integer';
	public const TYPE_OBJECT   = 'object';
	public const TYPE_STRING   = 'string';

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * -----------------------------------------------------------------------------
	 * Class constructor
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function __construct($value, string $type = null)
	{
		if ($type === null
			or ($type === self::TYPE_CALLABLE and is_callable($value))
			or ($type === self::TYPE_OBJECT and is_object($value))
			or gettype($value) === $type)
		{
			$this->value = $value;
		}
		else
		{
			throw new InvalidArgumentException('Incorrect type for value.');
		}
	}

	/**
	 * -----------------------------------------------------------------------------
	 *
	 * -----------------------------------------------------------------------------
	 *
	 * @since 2.0.0
	 */
	public function getValue(): mixed
	{
		return $this->value;
	}
}
