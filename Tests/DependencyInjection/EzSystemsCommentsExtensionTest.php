<?php

/**
 * File containing the EzSystemsCommentsExtensionTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle\Tests\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use EzSystems\CommentsBundle\DependencyInjection\EzSystemsCommentsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class EzSystemsCommentsExtensionTest extends AbstractExtensionTestCase
{
    private $availableSiteAccesses;

    private $groupsBySiteAccess;

    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver
     */
    private $configResolver;

    protected function setUp()
    {
        parent::setUp();
        $this->availableSiteAccesses = ['sa1', 'sa2', 'sa3'];
        ConfigurationProcessor::setAvailableSiteAccesses($this->availableSiteAccesses);
        $this->groupsBySiteAccess = [
            'sa2' => ['sa_group'],
            'sa3' => ['sa_group'],
        ];
        ConfigurationProcessor::setGroupsBySiteAccess($this->groupsBySiteAccess);
        $this->configResolver = new ConfigResolver($this->groupsBySiteAccess, 'ezsettings');
    }

    protected function getContainerExtensions()
    {
        return [new EzSystemsCommentsExtension()];
    }

    protected function load(array $configurationValues = [])
    {
        parent::load($configurationValues);
        $this->configResolver->setContainer($this->container);
    }

    public function testGetAlias()
    {
        $extension = new EzSystemsCommentsExtension();
        $this->assertSame('ez_comments', $extension->getAlias());
    }

    public function testNoConfig()
    {
        $this->load();

        foreach ($this->availableSiteAccesses as $sa) {
            $this->assertSame('no_comments', $this->configResolver->getParameter('default_provider', 'ez_comments', $sa));
            $this->assertSame([], $this->configResolver->getParameter('content_comments', 'ez_comments', $sa));
            $this->assertSame('EzSystemsCommentsBundle::disqus.html.twig', $this->configResolver->getParameter('disqus.default_template', 'ez_comments', $sa));
            $this->assertSame('EzSystemsCommentsBundle::facebook.html.twig', $this->configResolver->getParameter('facebook.default_template', 'ez_comments', $sa));
            $this->assertSame('light', $this->configResolver->getParameter('facebook.color_scheme', 'ez_comments', $sa));
            $this->assertTrue($this->configResolver->getParameter('facebook.include_sdk', 'ez_comments', $sa));
        }
    }

    public function testDefaultProvider()
    {
        $providerSa1 = 'disqus';
        $providerSaGroup = 'facebook';
        $config = [
            'system' => [
                'sa1' => [
                    'default_provider' => $providerSa1,
                ],
                'sa2' => [
                    'disqus' => ['shortname' => 'foo'],
                ],
                'sa_group' => [
                    'default_provider' => $providerSaGroup,
                ],
            ],
        ];
        $this->load($config);

        $this->assertSame($providerSa1, $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa1'));
        $this->assertSame($providerSaGroup, $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa2'));
        $this->assertSame($providerSaGroup, $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa3'));
    }

    public function testContentComments()
    {
        $providerSa1 = 'disqus';
        $contentCommentsSa1 = [
            'public_articles' => [
                'enabled' => true,
                'provider' => 'facebook',
                'match' => [
                    'Identifier\\ContentType' => ['article', 'blog_post'],
                    'Identifier\\Section' => 'standard',
                ],
                'options' => ['foo' => 'bar'],
            ],
            'private_articles' => [
                'enabled' => true,
                'provider' => 'disqus',
                'match' => [
                    'Identifier\\ContentType' => ['article', 'blog_post'],
                    'Identifier\\Section' => 'private',
                ],
                'options' => ['width' => 470],
            ],
        ];
        $expectedContentCommentsSa1 = ['comments' => $contentCommentsSa1];

        $providerSaGroup = 'facebook';
        $contentCommentsSaGroup = [
            'nights_watch_comments' => [
                'enabled' => false,
                'provider' => 'raven',
                'match' => [
                    'Identifier\\ContentType' => ['men_request', 'complaints'],
                ],
            ],
            'cersei_comments' => [
                'enabled' => true,
                'provider' => 'i_dont_care',
                'match' => [
                    'Identifier\\ContentType' => ['more_wine', 'more_blood'],
                    'Identifier\\Section' => 'private',
                ],
            ],
        ];
        $expectedCommentsSaGroup = [
            'comments' => [
                'nights_watch_comments' => [
                    'enabled' => false,
                    'provider' => 'raven',
                    'match' => [
                        'Identifier\\ContentType' => ['men_request', 'complaints'],
                    ],
                    'options' => [],
                ],
                'cersei_comments' => [
                    'enabled' => true,
                    'provider' => 'i_dont_care',
                    'match' => [
                        'Identifier\\ContentType' => ['more_wine', 'more_blood'],
                        'Identifier\\Section' => 'private',
                    ],
                    'options' => [],
                ],
            ],
        ];

        $providerSa2 = 'disqus';

        $contentCommentsSa3 = [
            'melisandre_comments' => [
                'enabled' => true,
                'provider' => 'stanis_baratheon',
                'match' => [
                    'God\Type' => 'fire_fire_FIRE',
                ],
            ],
            'cersei_comments' => [
                'enabled' => false,
            ],
            'nights_watch_comments' => [
                'enabled' => true,
                'provider' => 'raven',
                'match' => [
                    'Identifier\\ContentType' => ['men_request', 'complaints'],
                ],
            ],
        ];
        $expectedContentCommentsSa3 = [
            'comments' => [
                'nights_watch_comments' => [
                    'enabled' => true,
                    'provider' => 'raven',
                    'match' => [
                        'Identifier\\ContentType' => ['men_request', 'complaints'],
                    ],
                    'options' => [],
                ],
                'cersei_comments' => [
                    'enabled' => false,
                    'match' => [],
                    'options' => [],
                ],
                'melisandre_comments' => [
                    'enabled' => true,
                    'provider' => 'stanis_baratheon',
                    'match' => [
                        'God\\Type' => 'fire_fire_FIRE',
                    ],
                    'options' => [],
                ],
            ],
        ];

        $config = [
            'system' => [
                'sa1' => [
                    'default_provider' => $providerSa1,
                    'content_comments' => $contentCommentsSa1,
                ],
                'sa2' => [
                    'default_provider' => $providerSa2,
                ],
                'sa3' => [
                    'content_comments' => $contentCommentsSa3,
                ],
                'sa_group' => [
                    'default_provider' => $providerSaGroup,
                    'content_comments' => $contentCommentsSaGroup,
                ],
            ],
        ];
        $this->load($config);

        $this->assertSame($providerSa1, $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa1'));
        $this->assertEquals($expectedContentCommentsSa1, $this->configResolver->getParameter('content_comments', 'ez_comments', 'sa1'));

        $this->assertSame($providerSa2, $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa2'));
        $this->assertEquals($expectedCommentsSaGroup, $this->configResolver->getParameter('content_comments', 'ez_comments', 'sa2'));

        $this->assertSame($providerSaGroup, $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa3'));
        $this->assertEquals($expectedContentCommentsSa3, $this->configResolver->getParameter('content_comments', 'ez_comments', 'sa3'));
    }

    public function testDisqus()
    {
        $shortnameSa1 = 'nights_watch';
        $shortnameSaGroup = 'kings_landing';
        $templateSa1 = 'the_wall.html.twig';
        $defaultTemplate = 'EzSystemsCommentsBundle::disqus.html.twig';
        $config = [
            'system' => [
                'sa1' => [
                    'default_provider' => 'disqus',
                    'disqus' => [
                        'shortname' => $shortnameSa1,
                        'template' => $templateSa1,
                    ],
                ],
                'sa_group' => [
                    'default_provider' => 'disqus',
                    'disqus' => [
                        'shortname' => $shortnameSaGroup,
                    ],
                ],
            ],
        ];
        $this->load($config);

        $this->assertSame('disqus', $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa1'));
        $this->assertSame('disqus', $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa2'));
        $this->assertSame('disqus', $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa3'));
        $this->assertSame($shortnameSa1, $this->configResolver->getParameter('disqus.shortname', 'ez_comments', 'sa1'));
        $this->assertSame($templateSa1, $this->configResolver->getParameter('disqus.default_template', 'ez_comments', 'sa1'));
        $this->assertSame($shortnameSaGroup, $this->configResolver->getParameter('disqus.shortname', 'ez_comments', 'sa2'));
        $this->assertSame($shortnameSaGroup, $this->configResolver->getParameter('disqus.shortname', 'ez_comments', 'sa3'));
        $this->assertSame($defaultTemplate, $this->configResolver->getParameter('disqus.default_template', 'ez_comments', 'sa2'));
        $this->assertSame($defaultTemplate, $this->configResolver->getParameter('disqus.default_template', 'ez_comments', 'sa3'));
    }

    public function testFacebook()
    {
        $defaultWidth = 470;
        $appIdSa1 = 123;
        $colorSchemeSa1 = 'dark';
        $includeSdkSa1 = false;
        $defaultNumPosts = 10;
        $appIdSa2 = 456;
        $numPostsSa2 = 20;
        $includeSdkSa2 = true;
        $widthSa2 = 471;
        $appIdSaGroup = 789;
        $includeSdkSaGroup = false;
        $numPostsSaGroup = 15;
        $defaultTemplate = 'EzSystemsCommentsBundle::facebook.html.twig';
        $templateSa1 = 'tyron_half_face_book.html.twig';
        $templateSa2 = 'cerseis_facebook.html.twig';
        $widthSaGroup = 570;
        $config = [
            'system' => [
                'sa1' => [
                    'default_provider' => 'facebook',
                    'facebook' => [
                        'app_id' => $appIdSa1,
                        'color_scheme' => $colorSchemeSa1,
                        'include_sdk' => $includeSdkSa1,
                        'template' => $templateSa1,
                    ],
                ],
                'sa2' => [
                    'default_provider' => 'disqus',
                    'facebook' => [
                        'app_id' => $appIdSa2,
                        'num_posts' => $numPostsSa2,
                        'include_sdk' => $includeSdkSa2,
                        'template' => $templateSa2,
                        'width' => $widthSa2,
                    ],
                ],
                'sa_group' => [
                    'default_provider' => 'facebook',
                    'facebook' => [
                        'app_id' => $appIdSaGroup,
                        'include_sdk' => $includeSdkSaGroup,
                        'num_posts' => $numPostsSaGroup,
                        'width' => $widthSaGroup,
                    ],
                ],
            ],
        ];
        $this->load($config);

        $this->assertSame('facebook', $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa1'));
        $this->assertSame('disqus', $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa2'));
        $this->assertSame('facebook', $this->configResolver->getParameter('default_provider', 'ez_comments', 'sa3'));

        $this->assertSame($appIdSa1, $this->configResolver->getParameter('facebook.app_id', 'ez_comments', 'sa1'));
        $this->assertSame($appIdSa2, $this->configResolver->getParameter('facebook.app_id', 'ez_comments', 'sa2'));
        $this->assertSame($appIdSaGroup, $this->configResolver->getParameter('facebook.app_id', 'ez_comments', 'sa3'));

        $this->assertSame($colorSchemeSa1, $this->configResolver->getParameter('facebook.color_scheme', 'ez_comments', 'sa1'));
        $this->assertSame('light', $this->configResolver->getParameter('facebook.color_scheme', 'ez_comments', 'sa2'));
        $this->assertSame('light', $this->configResolver->getParameter('facebook.color_scheme', 'ez_comments', 'sa3'));

        $this->assertSame($includeSdkSa1, $this->configResolver->getParameter('facebook.include_sdk', 'ez_comments', 'sa1'));
        $this->assertSame($includeSdkSa2, $this->configResolver->getParameter('facebook.include_sdk', 'ez_comments', 'sa2'));
        $this->assertSame($includeSdkSaGroup, $this->configResolver->getParameter('facebook.include_sdk', 'ez_comments', 'sa3'));

        $this->assertSame($templateSa1, $this->configResolver->getParameter('facebook.default_template', 'ez_comments', 'sa1'));
        $this->assertSame($templateSa2, $this->configResolver->getParameter('facebook.default_template', 'ez_comments', 'sa2'));
        $this->assertSame($defaultTemplate, $this->configResolver->getParameter('facebook.default_template', 'ez_comments', 'sa3'));

        $this->assertSame($defaultWidth, $this->configResolver->getParameter('facebook.width', 'ez_comments', 'sa1'));
        $this->assertSame($widthSa2, $this->configResolver->getParameter('facebook.width', 'ez_comments', 'sa2'));
        $this->assertSame($widthSaGroup, $this->configResolver->getParameter('facebook.width', 'ez_comments', 'sa3'));

        $this->assertSame($defaultNumPosts, $this->configResolver->getParameter('facebook.num_posts', 'ez_comments', 'sa1'));
        $this->assertSame($numPostsSa2, $this->configResolver->getParameter('facebook.num_posts', 'ez_comments', 'sa2'));
        $this->assertSame($numPostsSaGroup, $this->configResolver->getParameter('facebook.num_posts', 'ez_comments', 'sa3'));
    }
}
