<?php
/**
 * File containing the Disqus configuration parser class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser;

use EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Disqus implements Parser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     *
     * @return void
     */
    public function addSemanticConfig( NodeBuilder $nodeBuilder )
    {
        $nodeBuilder
            ->arrayNode( 'disqus' )
                ->children()
                    ->scalarNode( 'shortname' )->isRequired()->info( 'Disqus "shortname"' )->end()
                    ->scalarNode( 'template' )->info( 'Template to use, overriding the built-in one.' )->end()
                ->end()
            ->end();
    }

    /**
     * Translates parsed semantic config values from $config to internal key/value pairs.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function registerInternalConfig( array $config, ContainerBuilder $container )
    {
        foreach ( $config['system'] as $sa => $settings )
        {
            if ( isset( $settings['disqus']['shortname'] ) )
                $container->setParameter( "ez_comments.$sa.disqus.shortname", $settings['disqus']['shortname'] );
            if ( isset( $settings['disqus']['template'] ) )
                $container->setParameter( "ez_comments.$sa.disqus.default_template", $settings['disqus']['template'] );
        }
    }
}
