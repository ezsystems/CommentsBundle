<?php
/**
 * File containing the CommentsRendererTest class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle\Tests;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\CommentsBundle\Comments\CommentsRenderer;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class CommentsRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface
     */
    private $matcherFactoryMock;

    protected function setUp()
    {
        parent::setUp();
        $this->matcherFactoryMock = $this->getMock(
            'eZ\\Publish\\Core\\MVC\\Symfony\\Matcher\\MatcherFactoryInterface'
        );
    }

    public function testConstruct()
    {
        $providers = array(
            'foo' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
            'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
        );
        $defaultRenderer = 'foo';
        $renderer = new CommentsRenderer( $this->matcherFactoryMock, $providers, $defaultRenderer );
        $this->assertSame( $providers, $renderer->getAllProviders() );
        $this->assertSame( $defaultRenderer, $renderer->getDefaultProviderLabel() );
    }

    public function testGetSetDefaultProviderLabel()
    {
        $renderer = new CommentsRenderer( $this->matcherFactoryMock );
        $this->assertNull( $renderer->getDefaultProviderLabel() );
        $renderer->setDefaultProviderLabel( 'foobar' );
        $this->assertSame( 'foobar', $renderer->getDefaultProviderLabel() );
    }

    public function testGetDefaultProvider()
    {
        $expectedProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $renderer = new CommentsRenderer(
            $this->matcherFactoryMock,
            array(
                'foo' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
                'bar' => $expectedProvider,
            ),
            'bar'
        );
        $this->assertSame( $expectedProvider, $renderer->getDefaultProvider() );
    }

    public function testGetDefaultProviderNoLabelSpecified()
    {
        $expectedProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $renderer = new CommentsRenderer(
            $this->matcherFactoryMock,
            array(
                'foo' => $expectedProvider,
                'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
            )
        );
        $this->assertSame( $expectedProvider, $renderer->getDefaultProvider() );
    }

    public function testAddGetProvider()
    {
        $renderer = new CommentsRenderer( $this->matcherFactoryMock );
        $this->assertEmpty( $renderer->getAllProviders() );
        $provider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $this->assertFalse( $renderer->hasProvider( 'foo' ) );
        $renderer->addProvider( $provider, 'foo' );
        $this->assertTrue( $renderer->hasProvider( 'foo' ) );
        $this->assertSame( $provider, $renderer->getProvider( 'foo' ) );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetInvalidProvider()
    {
        $renderer = new CommentsRenderer( $this->matcherFactoryMock );
        $this->assertEmpty( $renderer->getAllProviders() );
        $provider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $renderer->addProvider( $provider, 'foo' );
        $renderer->getProvider( 'bar' );
    }

    public function testRender()
    {
        $fooProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $providers = array(
            'foo' => $fooProvider,
            'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
        );
        $defaultRenderer = 'foo';
        $renderer = new CommentsRenderer( $this->matcherFactoryMock, $providers, $defaultRenderer );

        $request = new Request();
        $options = array( 'some' => 'thing' );
        $commentsList = 'I am a comment list';
        $fooProvider
            ->expects( $this->once() )
            ->method( 'render' )
            ->with( $request, $options )
            ->will( $this->returnValue( $commentsList ) );

        $this->assertSame( $commentsList, $renderer->render( $request, $options ) );
    }

    public function testRenderForContent()
    {
        $fooProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $providers = array(
            'foo' => $fooProvider,
            'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
        );
        $defaultProvider = 'foo';
        $renderer = new CommentsRenderer( $this->matcherFactoryMock, $providers, $defaultProvider );

        $contentInfo = new ContentInfo();
        $request = new Request();
        $options = array( 'some' => 'thing' );
        $commentsList = 'I am a comment list for a content';
        $fooProvider
            ->expects( $this->once() )
            ->method( 'renderForContent' )
            ->with( $contentInfo, $request, $options )
            ->will( $this->returnValue( $commentsList ) );

        $this->assertSame( $commentsList, $renderer->renderForContent( $contentInfo, $request, $options ) );
    }

    public function testRenderForContentDisabled()
    {
        $fooProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $providers = array(
            'foo' => $fooProvider,
            'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
        );
        $defaultProvider = 'foo';
        $renderer = new CommentsRenderer( $this->matcherFactoryMock, $providers, $defaultProvider );

        $contentInfo = new ContentInfo();
        $request = new Request();
        $options = array( 'some' => 'thing' );
        $fooProvider
            ->expects( $this->never() )
            ->method( 'renderForContent' );

        $this->matcherFactoryMock
            ->expects( $this->once() )
            ->method( 'match' )
            ->with( $contentInfo, 'comments' )
            ->will( $this->returnValue( array( 'enabled' => false ) ) );

        $this->assertNull( $renderer->renderForContent( $contentInfo, $request, $options ) );
    }

    public function testRenderForContentConfiguredProvider()
    {
        $defaultProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $barProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $renderer = new CommentsRenderer(
            $this->matcherFactoryMock,
            array(
                'foo' => $defaultProvider,
                'bar' => $barProvider,
            ),
            'foo'
        );

        $contentInfo = new ContentInfo();
        $request = new Request();
        $options = array( 'some' => 'thing' );
        $commentsList = 'I am a comment list from bar comments provider.';

        // Assume we have a configuration saying that we need to use "bar" provider
        $this->matcherFactoryMock
            ->expects( $this->once() )
            ->method( 'match' )
            ->with( $contentInfo, 'comments' )
            ->will( $this->returnValue( array( 'enabled' => true, 'provider' => 'bar' ) ) );

        // Default provider should not be called
        $defaultProvider
            ->expects( $this->never() )
            ->method( 'renderForContent' );

        $barProvider
            ->expects( $this->once() )
            ->method( 'renderForContent' )
            ->with( $contentInfo, $request, $options )
            ->will( $this->returnValue( $commentsList ) );

        $this->assertSame( $commentsList, $renderer->renderForContent( $contentInfo, $request, $options ) );
    }

    public function testCanCommentContent()
    {
        $renderer = new CommentsRenderer( $this->matcherFactoryMock );
        $contentInfo = new ContentInfo();

        $this->matcherFactoryMock
            ->expects( $this->once() )
            ->method( 'match' )
            ->with( $contentInfo, 'comments' )
            ->will( $this->returnValue( array( 'enabled' => true ) ) );

        $this->assertTrue( $renderer->canCommentContent( $contentInfo ) );
    }

    public function testCanNotCommentContent()
    {
        $renderer = new CommentsRenderer( $this->matcherFactoryMock );
        $contentInfo = new ContentInfo();

        $this->matcherFactoryMock
            ->expects( $this->once() )
            ->method( 'match' )
            ->with( $contentInfo, 'comments' )
            ->will( $this->returnValue( array( 'enabled' => false ) ) );

        $this->assertFalse( $renderer->canCommentContent( $contentInfo ) );
    }
}
