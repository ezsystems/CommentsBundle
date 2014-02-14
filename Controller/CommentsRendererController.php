<?php
/**
 * File containing the CommentsRendererController class.
 *
 * @copyright Copyright (C) 2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\CommentsBundle\Comments\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsRendererController
{
    /**
     * @var \EzSystems\CommentsBundle\Comments\ProviderInterface
     */
    private $commentsRenderer;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    public function __construct( ProviderInterface $commentsRenderer, ContentService $contentService )
    {
        $this->commentsRenderer = $commentsRenderer;
        $this->contentService = $contentService;
    }

    public function setRequest( Request $request = null )
    {
        $this->request = $request;
    }

    public function renderForContent( $contentId )
    {
        return new Response(
            $this->commentsRenderer->renderForContent(
                $this->contentService->loadContentInfo( $contentId ),
                $this->request
            )
        );
    }
}
