<?php

/**
 * File containing the ContentMatcherFactory class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle\Matcher;

use eZ\Publish\Core\MVC\Symfony\Matcher\ContentMatcherFactory as BaseFactory;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentMatcherFactory extends BaseFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $alwaysMatch = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $matchConfig = $this->container->get('ezpublish.config.resolver')->getParameter('content_comments', 'ez_comments');
        // If there is no matching rule, we consider that commenting is always allowed.
        if (empty($matchConfig)) {
            $this->alwaysMatch = true;
        }

        parent::__construct(
            $this->container->get('ezpublish.api.repository'),
            $matchConfig
        );
    }

    public function match(View $view)
    {
        if ($this->alwaysMatch === true) {
            return ['enabled' => true];
        }

        return parent::match($view);
    }

    /**
     * @param string $matcherIdentifier
     *
     * @return \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface
     */
    protected function getMatcher($matcherIdentifier)
    {
        if ($this->container->has($matcherIdentifier)) {
            return $this->container->get($matcherIdentifier);
        }

        return parent::getMatcher($matcherIdentifier);
    }
}
