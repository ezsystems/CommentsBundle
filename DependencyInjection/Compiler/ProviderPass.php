<?php
/**
 * File containing the ProviderPass class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LogicException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers comments providers.
 */
class ProviderPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \LogicException
     */
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ez_comments.renderer' ) )
            return;

        $commentsRendererDef = $container->getDefinition( 'ez_comments.renderer' );
        foreach ( $container->findTaggedServiceIds( 'ez_comments.provider' ) as $id => $attributes )
        {
            foreach ( $attributes as $attribute )
            {
                if ( !isset( $attribute['alias'] ) )
                    throw new LogicException( 'ez_comments.renderer service tag needs an "alias" attribute to identify the comments provider. None given' );

                $commentsRendererDef->addMethodCall(
                    'addProvider',
                    array ( new Reference( $id ), $attribute['alias'] )
                );
            }
        }
    }
}
