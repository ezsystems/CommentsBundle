<?php
/**
 * File containing the CommentsRenderer class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Comments;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\HttpFoundation\Request;
use InvalidArgumentException;

class CommentsRenderer implements ProviderInterface
{
    /**
     * Comments providers, indexed by their label.
     *
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @var string
     */
    private $defaultProvider;

    /**
     * @param ProviderInterface[] Comments providers, indexed by their label.
     * @param string|null $defaultProvider Label of provider to use by default. If not provided, the first entry in $providers will be used.
     */
    public function __construct( array $providers = array(), $defaultProvider = null )
    {
        $this->providers = $providers;
        $this->setDefaultProviderLabel( $defaultProvider );
    }

    /**
     * Sets the provider to use by default.
     *
     * @param string $defaultProvider Label of the provider to use.
     */
    public function setDefaultProviderLabel( $defaultProvider )
    {
        $this->defaultProvider = $defaultProvider;
    }

    /**
     * Returns the label of the default provider.
     *
     * @return null|string
     */
    public function getDefaultProviderLabel()
    {
        return $this->defaultProvider;
    }

    /**
     * Returns the default provider.
     * If no default provider is set, the first one will be returned.
     *
     * @return ProviderInterface
     */
    public function getDefaultProvider()
    {
        if ( isset( $this->defaultProvider ) )
        {
            return $this->getProvider( $this->defaultProvider );
        }

        $providerLabels = array_keys( $this->providers );
        return $this->providers[$providerLabels[0]];
    }

    /**
     * @param ProviderInterface $provider
     * @param string $label
     */
    public function addProvider( ProviderInterface $provider, $label )
    {
        $this->providers[$label] = $provider;
    }

    /**
     * Retrieves a comments provider by its label
     *
     * @param $label
     *
     * @return ProviderInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getProvider( $label )
    {
        if ( !isset( $this->providers[$label] ) )
        {
            throw new InvalidArgumentException( "Unknow comments provider '$label'" );
        }

        return $this->providers[$label];
    }

    /**
     * Returns all available providers
     *
     * @return ProviderInterface[]
     */
    public function getAllProviders()
    {
        return $this->providers;
    }

    /**
     * Renders the comments list.
     * Comment form might also be included.
     *
     * The default provider will be used unless one is specified in $options (with key 'provider')
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $options
     *
     * @return string
     */
    public function render( Request $request, array $options = array() )
    {
        $provider = isset( $options['provider'] ) ? $this->getProvider( $options['provider'] ) : $this->getDefaultProvider();
        unset( $options['provider'] );

        return $provider->render( $request, $options );
    }

    /**
     * Renders the comments list for a given content.
     * Comment form might also be included
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $options
     *
     * @return mixed
     */
    public function renderForContent( ContentInfo $contentInfo, Request $request, array $options = array() )
    {
        $provider = isset( $options['provider'] ) ? $this->getProvider( $options['provider'] ) : $this->getDefaultProvider();
        unset( $options['provider'] );

        return $provider->renderForContent( $contentInfo, $request, $options );
    }
}