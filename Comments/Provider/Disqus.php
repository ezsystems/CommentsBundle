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
use Symfony\Component\HttpFoundation\Request;

class Disqus extends TemplateBasedProvider
{
    /**
     * Disqus forum's shortname.
     *
     * @var string
     */
    protected $shortName;

    public function setShortName( $shortName )
    {
        $this->shortName = $shortName;
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
                'shortname' => $this->shortName,
                'identifier' => $request->getPathInfo(),
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
        return $this->doRender(
            $options + array(
                'shortname' => $this->shortName,
                'identifier' => $contentInfo->id,
                // TODO: Use translated name
                'title' => $contentInfo->name,
            )
        );
    }
}
