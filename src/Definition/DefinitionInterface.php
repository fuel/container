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

use Fuel\Container\ContainerAwareInterface;

/**
 * @since 2.0
 */
interface DefinitionInterface extends ContainerAwareInterface
{
    public function addArgument($arg): DefinitionInterface;
    public function addArguments(array $args): DefinitionInterface;
    public function addMethodCall(string $method, array $args = []): DefinitionInterface;
    public function addMethodCalls(array $methods = []): DefinitionInterface;
    public function addTag(string $tag): DefinitionInterface;
    public function getAlias(): string;
    public function getConcrete();
    public function hasTag(string $tag): bool;
    public function isShared(): bool;
    public function resolve(array $params = []);
    public function resolveNew(array $params = []);
    public function setAlias(string $id): DefinitionInterface;
    public function setConcrete($concrete): DefinitionInterface;
    public function setShared(bool $shared): DefinitionInterface;
}
