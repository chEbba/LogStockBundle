<?php
/*
 * Copyright (c) 2012
 * Kirill chEbba Cheunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\Bundle\LogStockBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * LogStockBundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('log_stock');

        $rootNode
            ->children()
                ->scalarNode('loader')->end()
                ->scalarNode('separator')->end()
                ->arrayNode('adapters')
                    ->fixXmlConfig('adapter')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')
                                ->isRequired()
                                ->beforeNormalization()
                                    ->always()
                                    ->then(function($v) { return strtolower($v); })
                                ->end()
                            ->end()
                            ->arrayNode('loggers')
                                ->fixXmlConfig('logger')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('id')->end()
                            ->scalarNode('channel')->end() // for the default monolog id
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
