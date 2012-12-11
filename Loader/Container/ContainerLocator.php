<?php
/*
 * Copyright (c) 2011
 * Kirill chEbba Cheunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\Bundle\LogStockBundle\Loader\Container;

use Che\LogStock\Loader\Container\ServiceLocator;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Dynamic Service Locator implementation for Symfony Container
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ContainerLocator implements ServiceLocator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Create new Service locator from Symfony2 Container
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getService($name)
    {
        return $this->container->get($name, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }
}
