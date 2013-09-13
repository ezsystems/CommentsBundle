<?php
/**
 * File containing the Disqus comments provider class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Comments\Provider;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\CommentsBundle\Comments\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

class Disqus implements ProviderInterface
{
    /**
     * Disqus forum's shortname.
     *
     * @var string
     */
    protected $shortName;

    /**
     * Template to use by default to render Disqus thread.
     *
     * @var string
     */
    private $defaultTemplate;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $templateEngine;

    public function __construct( EngineInterface $templateEngine, $defaultTemplate )
    {
        $this->templateEngine = $templateEngine;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function setShortName( $shortName )
    {
        $this->shortName = $shortName;
    }

    public function getDefaultTemplate()
    {
        return $this->defaultTemplate;
    }

    protected function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    /**
     * Renders the comments list.
     * Comment form might also be included.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $options
     *
     * @return string
     */
    public function render( Request $request, array $options = array() )
    {
        return $this->doRender(
            $options + array(
                'identifier' => $request->getPathInfo()
            )
        );
    }

    /**
     * Renders the comments list for a given content.
     * Comment form might also be included.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $options
     *
     * @return mixed
     */
    public function renderForContent( ContentInfo $contentInfo, Request $request, array $options = array() )
    {
        $siteAccessName = $request->attributes->has( 'siteaccess' ) ? $request->attributes->get( 'siteaccess' )->name : 'default';

        return $this->doRender(
            $options + array(
                'identifier' => $siteAccessName . '/' . $contentInfo->id,
                // TODO: Use translated name
                'title' => $contentInfo->name,
            )
        );
    }

    /**
     * Renders the template with provided options.
     * "template" option allows to override the default template for rendering.
     *
     * @param array $options
     * @return string
     */
    protected function doRender( array $options )
    {
        $template = isset( $options['template'] ) ? $options['template'] : $this->getDefaultTemplate();
        unset( $options['template'] );

        return $this->getTemplateEngine()->render( $template, $options );
    }
}
