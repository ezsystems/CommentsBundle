<?php
/**
 * File containing the DisqusTest class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Tests\Provider;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\CommentsBundle\Comments\Provider\Disqus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

class DisqusTest extends TemplateBasedProviderTest
{
    const SHORTNAME = 'eztest';

    /**
     * Returns the default template for the comments provider.
     * e.g. "disqus.html.twig".
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'disqus.html.twig';
    }

    /**
     * Returns the comments provider to test.
     *
     * @param \Symfony\Component\Templating\EngineInterface $templateEngine
     * @param $defaultTemplate
     *
     * @return \EzSystems\CommentsBundle\Comments\Provider\TemplateBasedProvider
     */
    protected function getCommentsProvider( EngineInterface $templateEngine, $defaultTemplate )
    {
        $provider = new Disqus( $templateEngine, $defaultTemplate );
        $provider->setShortName( static::SHORTNAME );
        return $provider;
    }

    /**
     * Returns the hash of options that is expected to be injected by the provider into the comments template,
     * given the Request object.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function getExpectedOptions( Request $request )
    {
        return array(
            'shortname' => static::SHORTNAME,
            'identifier' => $request->getPathInfo()
        );
    }

    /**
     * Returns the hash of options that is expected to be injected by the provider into the comments template,
     * given the ContentInfo and Request object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function getExpectedOptionsForContent( ContentInfo $contentInfo, Request $request )
    {
        return array(
            'shortname' => static::SHORTNAME,
            'identifier' => $contentInfo->id,
            'title' => $contentInfo->name
        );
    }
}
