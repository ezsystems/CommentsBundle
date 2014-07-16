<?php
/**
 * File containing the EzSystemsCommentsExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EzSystemsCommentsExtension extends Extension
{
    public function load( array $configs, ContainerBuilder $container )
    {
        $configuration = $this->getConfiguration( $configs, $container );
        $config = $this->processConfiguration( $configuration, $configs );

        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yml' );
        $loader->load( 'default_settings.yml' );

        $processor = new ConfigurationProcessor( $container, 'ez_comments' );
        $processor->mapConfig(
            $config,
            function ( $scopeSettings, $currentScope, ContextualizerInterface $contextualizer )
            {
                // Common settings
                if ( isset( $scopeSettings['default_provider'] ) )
                    $contextualizer->setContextualParameter( 'default_provider', $currentScope, $scopeSettings['default_provider'] );
                if ( isset( $scopeSettings['content_comments'] ) )
                    $contextualizer->setContextualParameter( 'content_comments', $currentScope, $scopeSettings['content_comments'] );

                // Disqus
                if ( isset( $scopeSettings['disqus']['shortname'] ) )
                    $contextualizer->setContextualParameter( 'disqus.shortname', $currentScope, $scopeSettings['disqus']['shortname'] );
                if ( isset( $scopeSettings['disqus']['template'] ) )
                    $contextualizer->setContextualParameter( 'disqus.default_template', $currentScope, $scopeSettings['disqus']['template'] );

                // Facebook
                if ( isset( $scopeSettings['facebook']['app_id'] ) )
                    $contextualizer->setContextualParameter( 'facebook.app_id', $currentScope, $scopeSettings['facebook']['app_id'] );
                if ( isset( $scopeSettings['facebook']['width'] ) )
                    $contextualizer->setContextualParameter( 'facebook.width', $currentScope, $scopeSettings['facebook']['width'] );
                if ( isset( $scopeSettings['facebook']['num_posts'] ) )
                    $contextualizer->setContextualParameter( 'facebook.num_posts', $currentScope, $scopeSettings['facebook']['num_posts'] );
                if ( isset( $scopeSettings['facebook']['color_scheme'] ) )
                    $contextualizer->setContextualParameter( 'facebook.color_scheme', $currentScope, $scopeSettings['facebook']['color_scheme'] );
                if ( isset( $scopeSettings['facebook']['include_sdk'] ) )
                    $contextualizer->setContextualParameter( 'facebook.include_sdk', $currentScope, $scopeSettings['facebook']['include_sdk'] );
                if ( isset( $scopeSettings['facebook']['template'] ) )
                    $contextualizer->setContextualParameter( 'facebook.default_template', $currentScope, $scopeSettings['facebook']['template'] );
            }
        );
    }

    public function getAlias()
    {
        return 'ez_comments';
    }
}
