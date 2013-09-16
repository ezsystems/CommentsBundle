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
    public function testContruct()
    {
        $providers = array(
            'foo' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
            'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
        );
        $defaultRenderer = 'foo';
        $renderer = new CommentsRenderer( $providers, $defaultRenderer );
        $this->assertSame( $providers, $renderer->getAllProviders() );
        $this->assertSame( $defaultRenderer, $renderer->getDefaultProviderLabel() );
    }

    public function testGetSetDefaultProviderLabel()
    {
        $renderer = new CommentsRenderer;
        $this->assertNull( $renderer->getDefaultProviderLabel() );
        $renderer->setDefaultProviderLabel( 'foobar' );
        $this->assertSame( 'foobar', $renderer->getDefaultProviderLabel() );
    }

    public function testGetDefaultProvider()
    {
        $expectedProvider = $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' );
        $renderer = new CommentsRenderer(
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
            array(
                'foo' => $expectedProvider,
                'bar' => $this->getMock( 'EzSystems\\CommentsBundle\\Comments\\ProviderInterface' ),
            )
        );
        $this->assertSame( $expectedProvider, $renderer->getDefaultProvider() );
    }

    public function testAddGetProvider()
    {
        $renderer = new CommentsRenderer;
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
        $renderer = new CommentsRenderer;
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
        $renderer = new CommentsRenderer( $providers, $defaultRenderer );

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
        $defaultRenderer = 'foo';
        $renderer = new CommentsRenderer( $providers, $defaultRenderer );

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
}
