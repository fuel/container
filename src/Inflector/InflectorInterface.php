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

namespace Fuel\Container\Inflector;

/**
 * @since 2.0
 */
interface InflectorInterface
{
	public function getType(): string;
	public function inflect(object $object): void;
	public function invokeMethod(string $name, array $args): InflectorInterface;
	public function invokeMethods(array $methods): InflectorInterface;
	public function setProperties(array $properties): InflectorInterface;
	public function setProperty(string $property, $value): InflectorInterface;
}
