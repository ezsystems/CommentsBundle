<?php
/**
 * File containing the Facebook comments provider class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Comments\Provider;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\HttpFoundation\Request;

class Facebook extends TemplateBasedProvider
{
    /**
     * Your Facebook application ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * Default width for the comments box.
     *
     * @var int
     */
    protected $defaultWidth;

    /**
     * Default number of comments to display.
     *
     * @var int
     */
    protected $defaultNumPosts;

    /**
     * Default color scheme to use for the comments box.
     * Only "light" or "dark" are possible.
     *
     * @var string
     */
    protected $defaultColorScheme;

    public function setAppId( $appId )
    {
        $this->appId = $appId;
    }

    public function setDefaultWidth( $defaultWidth )
    {
        $this->defaultWidth = $defaultWidth;
    }

    public function setDefaultNumPosts( $defaultNumPosts )
    {
        $this->defaultNumPosts = $defaultNumPosts;
    }

    public function setDefaultColorScheme( $defaultColorScheme )
    {
        $this->defaultColorScheme = $defaultColorScheme;
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
                'app_id' => $this->appId,
                'width' => $this->defaultWidth,
                'num_posts' => $this->defaultNumPosts,
                'color_scheme' => $this->defaultColorScheme,
                'url' => $request->getSchemeAndHttpHost() . $request->attributes->get( 'semanticPathinfo', $request->getPathInfo() )
            )
        );
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
        // TODO: Implement renderForContent() method.
    }
}
