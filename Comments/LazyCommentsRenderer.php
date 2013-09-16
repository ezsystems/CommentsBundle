<?php
/**
 * File containing the LazyCommentsRenderer class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Comments;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyCommentsRenderer extends CommentsRenderer
{
    private $container;

    /**
     * @var \Closure[]
     */
    private $lazyProviders = array();

    public function setContainer( ContainerInterface $container )
    {
        $this->container = $container;
    }

    /**
     * Registers a comments provider, the lazy way.
     * This avoids to have all providers built with there comments renderer, and thus avoids potential runtime config issues.
     *
     * @param string $providerId Provider service id in the service container
     * @param string $alias Provider's label
     */
    public function addLazyProvider( $providerId, $alias )
    {
        $container = $this->container;
        $this->lazyProviders[$alias] = function () use ( $container, $providerId )
        {
            return $container->get( $providerId );
        };
    }

    public function getProvider( $label )
    {
        if ( !$this->hasProvider( $label ) && isset( $this->lazyProviders[$label] ) )
        {
            $providerClosure = $this->lazyProviders[$label];
            $this->addProvider( $providerClosure(), $label );
        }

        return parent::getProvider( $label );
    }
}
