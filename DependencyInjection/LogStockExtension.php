<?php
/*
 * Copyright (c) 2012
 * Kirill chEbba Cheunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\Bundle\LogStockBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * LogStockBundle extension class
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class LogStockExtension extends Extension
{
    const ID_LOADER = 'log_stock.loader';
    const ID_ROOT = 'log_stock.adapter.root';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Change default loader
        if ($loaderId = $config['loader']) {
            $container->setAlias(self::ID_LOADER, $loaderId);
        }

        // Change default separator
        if ($separator = $config['separator']) {
            $container->setParameter('log_stock.loader.hierarchy.separator', $separator);
        }

        // Configure adapters and logger mapping
        foreach ($config['adapters'] as $name => $adapter) {
            // Create adapter
            $adapterId = $this->buildAdapter($container, $name, $adapter);
            // Alias adapter to logger
            foreach ($adapter['loggers'] as $logger) {
                $container->setAlias($this->getLoggetId($container, $logger), $adapterId);
            }
        }
    }

    /**
     * Create adapter definition
     *
     * @param ContainerBuilder $container
     * @param string           $name Adapter name
     * @param array            $adapter An array of adapter parameters
     *                                  [type, id, channel, limit]
     *
     * @return string
     */
    private function buildAdapter(ContainerBuilder $container, $name, array $adapter)
    {
        $type = $adapter['type'];
        $adapterId = $this->getAdapterId($name);
        $class = $this->getAdapterClassParameter($type);
        switch ($type) {
            case 'monolog':
                $id = $adapter['id'] ?: 'logger'; // use default logger
                $definition = new Definition($container->getParameter($class), array(new Reference($id)));
                // If channel option is set, use it as tag for monolog bundle
                if ($adapter['channel']) {
                    $definition->addTag('monolog.logger', array('channel' => $adapter['channel']));
                }
                $container->setDefinition($adapterId, $definition);
                return $adapterId;

            case 'system':
                $definition = new Definition($class);
                // If limit is set use it for levelLimit
                if ($adapter['limit']) {
                    $definition->setArguments(array(constant('Che\LogStock\Logger::'.$adapter['limit'])));
                }
                $container->setDefinition($adapterId, $definition);
                return $adapterId;

            case 'custom':
                if (!$adapter['id']) {
                    throw new InvalidArgumentException("Parameter 'id' is missed for 'custom' adapter");
                }
                return $adapter['id'];

            default:
                throw new InvalidArgumentException("Unknown adapter type '$type'");
        }
    }

    /**
     * Get the class name parameter by adapter type
     *
     * @param string $type
     *
     * @return string Classname parameter
     */
    private function getAdapterClassParameter($type)
    {
        return sprintf('log_stock.adapter.%s.class', $type);
    }

    /**
     * Get adapter id by adapter name
     *
     * @param string $name Adapter name
     *
     * @return string Id of adapter
     */
    private function getAdapterId($name)
    {
        return sprintf('log_stock.adapter.%s', $name);
    }

    /**
     * Get id for logger mapping
     *
     * @param ContainerBuilder $container
     * @param string           $name Logger name
     *
     * @return string
     */
    private function getLoggetId(ContainerBuilder $container, $name)
    {
        return ContainerIdFormatter::normalizeName(
            $name,
            $container->getParameter('log_stock.formatter.container.template'),
            $container->getParameter('log_stock.loader.hierarchy.separator')
        );
    }
}
