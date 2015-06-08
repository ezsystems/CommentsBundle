<?php
/**
 * File containing the CommentsRendererController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\CommentsBundle\Comments\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders comments lists and/or forms.
 */
class CommentsRendererController
{
    /** @var \EzSystems\CommentsBundle\Comments\ProviderInterface */
    private $commentsRenderer;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct( ProviderInterface $commentsRenderer, ContentService $contentService )
    {
        $this->commentsRenderer = $commentsRenderer;
        $this->contentService = $contentService;
    }

    /**
     * Renders the comments list for content with id $contentId
     * Comment form might also be included
     *
     * @param mixed $contentId
     *
     * @return Response
     */
    public function renderForContentAction( $contentId, Request $request )
    {
        return new Response(
            $this->commentsRenderer->renderForContent(
                $this->contentService->loadContentInfo( $contentId ),
                $request
            )
        );
    }
}
