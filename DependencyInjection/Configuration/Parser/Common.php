<?php
/**
 * File containing the Common configuration parser class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
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
            ->end()
            ->arrayNode( 'content_comments' )
                ->info( 'Rules for comments on Content objects. If none provided, commenting will be allowed for any type of content.' )
                ->example(
                    array(
                        'public_articles' => array(
                            'enabled' => true,
                            'provider' => 'facebook',
                            'match' => array(
                                'Identifier\\ContentType' => array( 'article', 'blog_post' ),
                                'Identifier\\Section' => 'standard',
                            )
                        ),
                        'private_articles' => array(
                            'enabled' => true,
                            'provider' => 'disqus',
                            'match' => array(
                                'Identifier\\ContentType' => array( 'article', 'blog_post' ),
                                'Identifier\\Section' => 'private',
                            )
                        )
                    )
                )
                ->useAttributeAsKey( "my_comment_ruleset" )
                ->prototype( "array" )
                    ->normalizeKeys( false )
                    ->children()
                        ->booleanNode( "enabled" )->info( "Indicates if comments are enabled or not. Default is true" )->defaultTrue()->end()
                        ->scalarNode( "provider" )->info( "Provider to use. Default is configured default_provider" )->end()
                        ->arrayNode( "options" )
                            ->info( 'Provider specific options. See available options for your provider.' )
                            ->prototype( 'variable' )->end()
                        ->end()
                        ->arrayNode( "match" )
                            ->info( 'Condition matchers configuration. You can use the same matchers as for selecting content view templates.' )
                            ->example( array( 'Identifier\\Contentype' => array( 'article', 'blog_post' ) ) )
                            ->useAttributeAsKey( "matcher" )
                            ->prototype( "variable" )->end()
                        ->end()
                    ->end()
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
            if ( isset( $settings['default_provider'] ) )
                $container->setParameter( "ez_comments.$sa.default_provider", $settings['default_provider'] );

            if ( isset( $settings['content_comments'] ) )
                $container->setParameter( "ez_comments.$sa.content_comments", array( 'comments' => $settings['content_comments'] ) );
        }
    }
}
