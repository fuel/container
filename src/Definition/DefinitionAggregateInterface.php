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

use IteratorAggregate;
use Fuel\Container\ContainerAwareInterface;

/**
 * @since 2.0
 */
interface DefinitionAggregateInterface extends ContainerAwareInterface, IteratorAggregate
{
    public function add(string $id, $definition): DefinitionInterface;
    public function addShared(string $id, $definition): DefinitionInterface;
    public function getDefinition(string $id): DefinitionInterface;
    public function has(string $id): bool;
    public function hasTag(string $tag): bool;
    public function resolve(string $id, array $params = []);
    public function resolveNew(string $id, array $params = []);
    public function resolveTagged(string $tag): array;
    public function resolveTaggedNew(string $tag): array;
}
