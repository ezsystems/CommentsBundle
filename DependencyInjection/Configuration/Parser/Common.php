<?php
/**
 * File containing the Common configuration parser class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Common implements Parser
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
            ->scalarNode( 'default_provider' )
                ->isRequired()
                ->info( 'Comments provider you want to use by default (e.g. "disqus").' )
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
            if ( isset( $settings['default_provider'] ) )
                $container->setParameter( "ez_comments.$sa.default_provider", $settings['default_provider'] );
        }
    }
}
