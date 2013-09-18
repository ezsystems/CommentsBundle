<?php
/**
 * File containing the AuthorizerInterface class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Comments;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for comment authorizers that are based on Content.
 * Comments authorizers check if one can comment, based on ContentInfo.
 */
interface ContentAuthorizerInterface
{
    /**
     * Returns true if it comments can be appended to a content, based on its ContentInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return bool
     */
    public function canCommentContent( ContentInfo $contentInfo );
}
