<?php

/**
 * File containing the ProviderTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle\Tests\Provider;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

abstract class TemplateBasedProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Templating\EngineInterface
     */
    protected $templateEngine;

    protected function setUp()
    {
        parent::setUp();
        $this->templateEngine = $this->createMock(EngineInterface::class);
    }

    /**
     * Returns the default template for the comments provider.
     * e.g. "disqus.html.twig".
     *
     * @return string
     */
    abstract protected function getDefaultTemplate();

    /**
     * Returns the comments provider to test.
     *
     * @param \Symfony\Component\Templating\EngineInterface $templateEngine
     * @param $defaultTemplate
     *
     * @return \EzSystems\CommentsBundle\Comments\Provider\TemplateBasedProvider
     */
    abstract protected function getCommentsProvider(EngineInterface $templateEngine, $defaultTemplate);

    public function testSetGetDefaultTemplate()
    {
        $defaultTemplate = $this->getDefaultTemplate();
        $provider = $this->getCommentsProvider($this->templateEngine, $defaultTemplate);
        $this->assertSame($defaultTemplate, $provider->getDefaultTemplate());
        $this->assertSame($this->templateEngine, $provider->getTemplateEngine());

        $newDefaultTemplate = 'foo.html.twig';
        $provider->setDefaultTemplate($newDefaultTemplate);
        $this->assertSame($newDefaultTemplate, $provider->getDefaultTemplate());
    }

    /**
     * Returns the hash of options that is expected to be injected by the provider into the comments template,
     * given the Request object.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    abstract protected function getExpectedOptions(Request $request);

    /**
     * @dataProvider renderTestProvider
     *
     * @param Request $request
     * @param array $options Minimal options the provider is supposed to inject into its template
     * @param array $customOptions Custom options injected by higher call (e.g. from the template helper)
     */
    public function testRender(Request $request, array $options, array $customOptions = [])
    {
        $renderedComments = "Guess what? I'm a comments thread!";
        $defaultTemplate = $this->getDefaultTemplate();

        $this->templateEngine
            ->expects($this->once())
            ->method('render')
            ->with($defaultTemplate, $customOptions + $options)
            ->willReturn($renderedComments);

        $this->assertSame(
            $renderedComments,
            $this
                ->getCommentsProvider($this->templateEngine, $defaultTemplate)
                ->render($request, $customOptions)
        );
    }

    /**
     * @dataProvider renderTestProvider
     *
     * @param Request $request
     * @param array $options Minimal options the provider is supposed to inject into its template
     * @param array $customOptions Custom options injected by higher call (e.g. from the template helper)
     */
    public function testRenderTemplateOverride(Request $request, array $options, array $customOptions = [])
    {
        $renderedComments = "Guess what? I'm a comments thread!";
        $template = 'override.html.twig';

        $this->templateEngine
            ->expects($this->once())
            ->method('render')
            ->with($template, $customOptions + $options)
            ->willReturn($renderedComments);

        $this->assertSame(
            $renderedComments,
            $this
                ->getCommentsProvider($this->templateEngine, $template)
                ->render($request, $customOptions + ['template' => $template])
        );
    }

    public function renderTestProvider()
    {
        $request1 = Request::create('/foo/bar');
        $options1 = $this->getExpectedOptions($request1);
        $customOptions1 = ['category' => '123456789'];

        $request2 = Request::create('/mySiteAccess/some/thing', 'GET', ['foo' => 'bar']);
        $request2->attributes->set('siteaccess', new SiteAccess('mySiteAccess', 'uri'));
        $request2->attributes->set('semanticPathinfo', '/some/thing');
        $options2 = $this->getExpectedOptions($request2);
        $customOptions2 = ['category' => '123456789', 'lonely_var' => "I'm a poooooor lonesome cow-boy!"];

        return [
            [$request1, $options1, $customOptions1],
            [$request2, $options2, $customOptions2],
        ];
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
    abstract protected function getExpectedOptionsForContent(ContentInfo $contentInfo, Request $request);

    /**
     * @dataProvider renderForContentTestProvider
     *
     * @param ContentInfo $contentInfo
     * @param Request $request
     * @param array $options Minimal options the provider is supposed to inject into its template
     * @param array $customOptions Custom options injected by higher call (e.g. from the template helper)
     */
    public function testRenderForContent(ContentInfo $contentInfo, Request $request, array $options, array $customOptions = [])
    {
        $renderedComments = "I'm a comments thread for $contentInfo->id!";
        $defaultTemplate = $this->getDefaultTemplate();

        $this->templateEngine
            ->expects($this->once())
            ->method('render')
            ->with($defaultTemplate, $customOptions + $options)
            ->willReturn($renderedComments);

        $this->assertSame(
            $renderedComments,
            $this
                ->getCommentsProvider($this->templateEngine, $defaultTemplate)
                ->renderForContent($contentInfo, $request, $customOptions)
        );
    }

    /**
     * @dataProvider renderForContentTestProvider
     *
     * @param ContentInfo $contentInfo
     * @param Request $request
     * @param array $options
     * @param array $customOptions
     */
    public function testRenderForContentTemplateOverride(ContentInfo $contentInfo, Request $request, array $options, array $customOptions = [])
    {
        $renderedComments = "I'm a comments thread for $contentInfo->id!";
        $template = 'override.html.twig';

        $this->templateEngine
            ->expects($this->once())
            ->method('render')
            ->with($template, $customOptions + $options)
            ->willReturn($renderedComments);

        $this->assertSame(
            $renderedComments,
            $this
                ->getCommentsProvider($this->templateEngine, $template)
                ->renderForContent($contentInfo, $request, $customOptions + ['template' => $template])
        );
    }

    public function renderForContentTestProvider()
    {
        $ret = [];

        $contentInfo1 = new ContentInfo(['id' => 123, 'mainLocationId' => 456, 'name' => 'A developer walks into a bar']);
        $request1 = Request::create('/foo/bar');
        $ret[] = [
            $contentInfo1,
            $request1,
            $this->getExpectedOptionsForContent($contentInfo1, $request1),
        ];

        $contentInfo2 = new ContentInfo(['id' => 456, 'mainLocationId' => 789, 'name' => 'Again a fake content']);
        $request2 = Request::create('/test/fake-content');
        $ret[] = [
            $contentInfo2,
            $request2,
            $this->getExpectedOptionsForContent($contentInfo2, $request2),
            ['category' => '123456789'],
        ];

        $contentInfo3 = new ContentInfo(['id' => 789, 'mainLocationId' => 123, 'name' => "It's a kind of Magic"]);
        $request3 = Request::create('/queen/kind-of-magic/(foo)/bar', 'GET', ['some' => 'thing']);
        $ret[] = [
            $contentInfo3,
            $request3,
            $this->getExpectedOptionsForContent($contentInfo3, $request3),
            ['category' => '123456789', 'lonely_var' => "I'm a poooooor lonesome cow-boy!"],
        ];

        return $ret;
    }
}
