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

use Fuel\Container\Exception\NotFoundException;
/**
 * @since 2.0
 */
class RuntimeValueArgument implements RuntimeValueInterface
{
    protected $name;

    protected $values = [];

    /**
     * -----------------------------------------------------------------------------
     * Class constructor
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function __construct(string $name, mixed $default = null)
    {
        $this->name = $name;

        if (func_num_args() > 1)
        {
            $this->values['default'] = $default;
        }
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * -----------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------
     *
     * @since 2.0.0
     */
    public function getDefault(): mixed
    {
        if (array_key_exists('default', $this->values))
        {
            return $this->values['default'];
        }

        throw new NotFoundException(sprintf('No runtime value exists for "%s".', $this->name));
    }
}
