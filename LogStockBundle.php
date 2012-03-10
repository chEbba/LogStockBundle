<?php
/*
 * Copyright (c) 2012
 * Kirill chEbba Cheunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\Bundle\LogStockBundle;

use Che\LogStock\LoggerFactory;
use Che\Bundle\LogStockBundle\DependencyInjection\LogStockExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle class for LogStock integration
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class LogStockBundle extends Bundle
{
    public function boot()
    {
        // Init loader from container
        $loader = $this->container->get(LogStockExtension::ID_LOADER, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if ($loader) {
            LoggerFactory::initLoader($loader);
        }

        // Init root adapter if set
        $rootAdapter = $this->container->get(LogStockExtension::ID_ROOT, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if ($rootAdapter) {
            LoggerFactory::initRootAdapter($rootAdapter);
        }
    }
}
