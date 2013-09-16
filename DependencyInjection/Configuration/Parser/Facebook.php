<?php
/**
 * File containing the Facebook configuration parser class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser;

use EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Facebook implements Parser
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
            ->arrayNode( 'facebook' )
                ->children()
                    ->scalarNode( 'app_id' )->isRequired()->info( 'Facebook application ID' )->end()
                    ->scalarNode( 'width' )->info( 'Width for the comments box (default is 470)' )->end()
                    ->scalarNode( 'num_posts' )->info( 'Number of comments to display (default is 10)' )->end()
                    ->enumNode( 'color_scheme' )->info( 'Color scheme to use (can be "light" or "dark"). Default is "light"' )->values( array( 'light', 'dark' ) )->end()
                    ->booleanNode( 'include_sdk' )->info( 'Whether to include Facebook JS SDK with the comments rendering. If set to false, you must include it on your own. Default is true.' )->end()
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
            if ( isset( $settings['facebook']['app_id'] ) )
                $container->setParameter( "ez_comments.$sa.facebook.app_id", $settings['facebook']['app_id'] );
            if ( isset( $settings['facebook']['width'] ) )
                $container->setParameter( "ez_comments.$sa.facebook.width", $settings['facebook']['width'] );
            if ( isset( $settings['facebook']['num_posts'] ) )
                $container->setParameter( "ez_comments.$sa.facebook.num_posts", $settings['facebook']['num_posts'] );
            if ( isset( $settings['facebook']['color_scheme'] ) )
                $container->setParameter( "ez_comments.$sa.facebook.color_scheme", $settings['facebook']['color_scheme'] );
            if ( isset( $settings['facebook']['include_sdk'] ) )
                $container->setParameter( "ez_comments.$sa.facebook.include_sdk", $settings['facebook']['include_sdk'] );
            if ( isset( $settings['facebook']['template'] ) )
                $container->setParameter( "ez_comments.$sa.facebook.default_template", $settings['facebook']['template'] );
        }
    }
}
