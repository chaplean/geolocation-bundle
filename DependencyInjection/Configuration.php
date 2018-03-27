<?php

namespace Chaplean\Bundle\GeolocationBundle\DependencyInjection;

use Chaplean\Bundle\GeolocationBundle\Entity\Address;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('chaplean_geolocation');

        $rootNode->children()
            ->arrayNode('persist_entity')
                ->children()
                    ->scalarNode('address')->defaultValue(Address::class)->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
