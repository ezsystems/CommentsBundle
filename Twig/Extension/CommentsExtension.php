<?php
/**
 * File containing the CommentsExtension class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Twig\Extension;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\CommentsBundle\Comments\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig_Extension;
use Twig_SimpleFunction;
use RuntimeException;

class CommentsExtension extends Twig_Extension
{
    /**
     * @var \EzSystems\CommentsBundle\Comments\ProviderInterface
     */
    private $commentsRenderer;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function __construct( ProviderInterface $commentsRenderer )
    {
        $this->commentsRenderer = $commentsRenderer;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ez_comments';
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ez_comments_render',
                array( $this, 'render' ),
                array( 'is_safe' => 'html' )
            ),
            new Twig_SimpleFunction(
                'ez_comments_render_content',
                array( $this, 'renderForContent' ),
                array( 'is_safe' => 'html' )
            )
        );
    }

    public function setRequest( Request $request = null )
    {
        $this->request = $request;
    }

    /**
     * Triggers comments rendering
     *
     * @param array $options
     * @param string|null $provider Label of the provider to use. If null, the default provider will be used.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function render( array $options = array(), $provider = null )
    {
        if ( isset( $provider ) )
        {
            $options['provider'] = $provider;
        }

        if ( !isset( $this->request ) )
        {
            throw new RuntimeException( 'Comments rendering needs the Request.' );
        }

        return $this->commentsRenderer->render( $this->request, $options );
    }

    /**
     * Triggers comments rendering for a given ContentInfo object.
     *
     * @param ContentInfo $contentInfo
     * @param array $options
     * @param string|null $provider Label of the provider to use. If null, the default provider will be used.
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function renderForContent( ContentInfo $contentInfo, array $options = array(), $provider = null )
    {
        if ( isset( $provider ) )
        {
            $options['provider'] = $provider;
        }

        if ( !isset( $this->request ) )
        {
            throw new RuntimeException( 'Comments rendering needs the Request.' );
        }

        return $this->commentsRenderer->renderForContent( $contentInfo, $this->request, $options );
    }
}
