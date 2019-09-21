<?php

/**
 * File containing the CommentsRendererTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle\Tests;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use EzSystems\CommentsBundle\Comments\CommentsRenderer;
use EzSystems\CommentsBundle\Comments\ProviderInterface as CommentsProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CommentsRendererTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface */
    private $matcherFactoryMock;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $configResolverMock;

    /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit_Framework_MockObject_MockObject */
    private $contentServiceMock;

    protected function setUp()
    {
        parent::setUp();
        $this->matcherFactoryMock = $this->createMock(MatcherFactoryInterface::class);
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);

        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentByContentInfo')
            ->willReturn(new Content());
    }

    public function testConstruct()
    {
        $providers = [
            'foo' => $this->createCommentsProviderMock(),
            'bar' => $this->createCommentsProviderMock(),
        ];
        $defaultRenderer = 'foo';
        $renderer = $this->createCommentsRenderer($providers, $defaultRenderer);
        $this->assertSame($providers, $renderer->getAllProviders());
        $this->assertSame($defaultRenderer, $renderer->getDefaultProviderLabel());
    }

    public function testGetSetDefaultProviderLabel()
    {
        $renderer = $this->createCommentsRenderer();
        $this->assertNull($renderer->getDefaultProviderLabel());
        $renderer->setDefaultProviderLabel('foobar');
        $this->assertSame('foobar', $renderer->getDefaultProviderLabel());
    }

    public function testGetDefaultProvider()
    {
        $expectedProvider = $this->createCommentsProviderMock();
        $renderer = $this->createCommentsRenderer(
            [
                'foo' => $this->createCommentsProviderMock(),
                'bar' => $expectedProvider,
            ],
            'bar'
        );
        $this->assertSame($expectedProvider, $renderer->getDefaultProvider());
    }

    public function testGetDefaultProviderNoLabelSpecified()
    {
        $expectedProvider = $this->createCommentsProviderMock();
        $renderer = $this->createCommentsRenderer(
            [
                'foo' => $expectedProvider,
                'bar' => $this->createCommentsProviderMock(),
            ]
        );
        $this->assertSame($expectedProvider, $renderer->getDefaultProvider());
    }

    public function testAddGetProvider()
    {
        $renderer = $this->createCommentsRenderer();
        $this->assertEmpty($renderer->getAllProviders());
        $provider = $this->createCommentsProviderMock();
        $this->assertFalse($renderer->hasProvider('foo'));
        $renderer->addProvider($provider, 'foo');
        $this->assertTrue($renderer->hasProvider('foo'));
        $this->assertSame($provider, $renderer->getProvider('foo'));
    }

    public function testGetInvalidProvider()
    {
        $this->expectException(\InvalidArgumentException::class);

        $renderer = $this->createCommentsRenderer();
        $this->assertEmpty($renderer->getAllProviders());
        $provider = $this->createCommentsProviderMock();
        $renderer->addProvider($provider, 'foo');
        $renderer->getProvider('bar');
    }

    public function testRender()
    {
        $fooProvider = $this->createCommentsProviderMock();
        $providers = [
            'foo' => $fooProvider,
            'bar' => $this->createCommentsProviderMock(),
        ];
        $defaultRenderer = 'foo';
        $renderer = $this->createCommentsRenderer($providers, $defaultRenderer);

        $request = new Request();
        $options = ['some' => 'thing'];
        $commentsList = 'I am a comment list';
        $fooProvider
            ->expects($this->once())
            ->method('render')
            ->with($request, $options)
            ->willReturn($commentsList);

        $this->assertSame($commentsList, $renderer->render($request, $options));
    }

    public function testRenderForContent()
    {
        $fooProvider = $this->createCommentsProviderMock();
        $providers = [
            'foo' => $fooProvider,
            'bar' => $this->createCommentsProviderMock(),
        ];
        $defaultProvider = 'foo';
        $renderer = $this->createCommentsRenderer($providers, $defaultProvider);

        $contentInfo = new ContentInfo();
        $request = new Request();
        $options = ['some' => 'thing'];
        $commentsList = 'I am a comment list for a content';
        $fooProvider
            ->expects($this->once())
            ->method('renderForContent')
            ->with($contentInfo, $request, $options)
            ->willReturn($commentsList);

        $this->assertSame($commentsList, $renderer->renderForContent($contentInfo, $request, $options));
    }

    public function testRenderForContentDisabled()
    {
        $fooProvider = $this->createCommentsProviderMock();
        $providers = [
            'foo' => $fooProvider,
            'bar' => $this->createCommentsProviderMock(),
        ];
        $defaultProvider = 'foo';
        $renderer = $this->createCommentsRenderer($providers, $defaultProvider);

        $request = new Request();
        $options = ['some' => 'thing'];
        $fooProvider
            ->expects($this->never())
            ->method('renderForContent');

        $this->matcherFactoryMock
            ->expects($this->once())
            ->method('match')
            ->willReturn(['enabled' => false]);

        $this->assertNull($renderer->renderForContent(new ContentInfo(), $request, $options));
    }

    public function testRenderForContentConfiguredProvider()
    {
        $defaultProvider = $this->createCommentsProviderMock();
        $barProvider = $this->createCommentsProviderMock();
        $renderer = $this->createCommentsRenderer(['foo' => $defaultProvider, 'bar' => $barProvider], 'foo');

        $contentInfo = new ContentInfo();
        // Assume we have a configuration saying that we need to use "bar" provider
        $this->matcherFactoryMock
            ->expects($this->once())
            ->method('match')
            ->willReturn(
                    [
                        'enabled' => true,
                        'provider' => 'bar',
                        'options' => ['foo' => 'This should be overridden', 'some_configured_option' => 'some_value'],
                    ]
            );

        // Default provider should not be called
        $defaultProvider
            ->expects($this->never())
            ->method('renderForContent');

        $request = new Request();
        $commentsList = 'I am a comment list from bar comments provider.';
        $explicitOptions = ['some' => 'thing', 'foo' => 'bar'];
        // $expectedOptions are a mix between explicitly passed options and configured options.
        // Explicit options have precedence.
        $expectedOptions = [
            'some' => 'thing',
            'foo' => 'bar',
            'some_configured_option' => 'some_value',
        ];
        $barProvider
            ->expects($this->once())
            ->method('renderForContent')
            ->with($contentInfo, $request, $expectedOptions)
            ->willReturn($commentsList);

        $this->assertSame($commentsList, $renderer->renderForContent($contentInfo, $request, $explicitOptions));
    }

    public function testCanCommentContent()
    {
        $renderer = $this->createCommentsRenderer();
        $contentInfo = new ContentInfo();

        $this->matcherFactoryMock
            ->expects($this->once())
            ->method('match')
            ->willReturn(['enabled' => true]);

        $this->assertTrue($renderer->canCommentContent($contentInfo));
    }

    public function testCanNotCommentContent()
    {
        $renderer = $this->createCommentsRenderer();
        $contentInfo = new ContentInfo();

        $this->matcherFactoryMock
            ->expects($this->once())
            ->method('match')
            ->willReturn(['enabled' => false]);

        $this->assertFalse($renderer->canCommentContent($contentInfo));
    }

    /**
     * @param $providers
     * @param $defaultProvider
     *
     * @return \EzSystems\CommentsBundle\Comments\CommentsRenderer
     */
    private function createCommentsRenderer(array $providers = [], $defaultProvider = null)
    {
        return new CommentsRenderer(
            $this->matcherFactoryMock,
            $this->configResolverMock,
            $this->contentServiceMock,
            $providers,
            $defaultProvider
        );
    }

    protected function createCommentsProviderMock()
    {
        return $this->createMock(CommentsProviderInterface::class);
    }
}
