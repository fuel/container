<?php

declare(strict_types=1);

namespace League\Container;

use League\Container\Exception\ContainerException;

trait ContainerAwareTrait
{
    /**
     * @var DefinitionContainerInterface
     */
    protected $container;

    /**
     * @var Container
     */
    protected $leagueContainer;

    /**
     * Set a container.
     *
     * @param DefinitionContainerInterface $container
     *
     * @return ContainerAwareInterface
     */
    public function setContainer(DefinitionContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Get the container.
     *
     * @return DefinitionContainerInterface
     */
    public function getContainer(): DefinitionContainerInterface
    {
        if ($this->container instanceof DefinitionContainerInterface) {
            return $this->container;
        }

        throw new ContainerException('No container implementation has been set.');
    }
}
