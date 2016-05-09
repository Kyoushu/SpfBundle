<?php

namespace Kyoushu\SpfBundle\DependencyInjection;

use Kyoushu\SpfBundle\Templating\Fragment;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kyoushu_spf');

        $this->buildDefaultFragments($rootNode);

        return $treeBuilder;
    }

    protected function buildDefaultFragments(ArrayNodeDefinition $rootNode)
    {
        /** @var NodeDefinition|ArrayNodeDefinition $node */
        $node = $rootNode->children()->arrayNode('default_fragments')->prototype('array');

        $node->children()->scalarNode('name')->isRequired();
        $node->children()->scalarNode('type')->isRequired();
        $node->children()->scalarNode('value')->defaultNull();
    }

}