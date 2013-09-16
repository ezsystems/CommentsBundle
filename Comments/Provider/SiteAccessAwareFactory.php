<?php
/**
 * File containing the SiteAccessAwareFactory class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Comments\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Templating\EngineInterface;

class SiteAccessAwareFactory
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templateEngine;

    public function __construct( ConfigResolverInterface $configResolver, EngineInterface $templateEngine )
    {
        $this->configResolver = $configResolver;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param string $disqusProviderClass
     *
     * @return \EzSystems\CommentsBundle\Comments\ProviderInterface
     */
    public function buildDisqus( $disqusProviderClass )
    {
        /** @var \EzSystems\CommentsBundle\Comments\Provider\Disqus $disqusProvider */
        $disqusProvider = new $disqusProviderClass();
        $disqusProvider->setTemplateEngine( $this->templateEngine );
        $disqusProvider->setDefaultTemplate(
            $this->configResolver->getParameter( 'disqus.default_template', 'ez_comments' )
        );
        $disqusProvider->setShortName( $this->configResolver->getParameter( 'disqus.shortname', 'ez_comments' ) );

        return $disqusProvider;
    }
}
