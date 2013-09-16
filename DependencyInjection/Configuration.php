<?php

namespace EzSystems\CommentsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
     * @var \EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser[]
     */
    private $configParsers;

    public function __construct( array $configParsers )
    {
        $this->configParsers = $configParsers;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root( 'ez_comments' );

        $this->addSystemSection( $rootNode );

        return $treeBuilder;
    }

    private function addSystemSection( ArrayNodeDefinition $rootNode )
    {
        $systemNodeBuilder = $rootNode
            ->children()
                ->arrayNode( 'system' )
                    ->info( 'System configuration. First key is always a siteaccess or siteaccess group name' )
                    ->useAttributeAsKey( 'siteaccess_name' )
                    ->normalizeKeys( false )
                    ->prototype( 'array' )
                        ->children();

        // Delegate to configuration parsers
        foreach ( $this->configParsers as $parser )
        {
            $parser->addSemanticConfig( $systemNodeBuilder );
        }
    }
}
