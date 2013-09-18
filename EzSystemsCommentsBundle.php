<?php
/**
 * File containing the EzSystemsCommentsBundle class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\CommentsBundle;

use EzSystems\CommentsBundle\DependencyInjection\Compiler\ProviderPass;
use EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser\Common;
use EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser\Facebook;
use EzSystems\CommentsBundle\DependencyInjection\EzSystemsCommentsExtension;
use EzSystems\CommentsBundle\DependencyInjection\Configuration\Parser\Disqus as DisqusConfigParser;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsCommentsBundle extends Bundle
{
    public function build( ContainerBuilder $container )
    {
        $container->addCompilerPass( new ProviderPass() );
        parent::build( $container );
    }

    public function getContainerExtension()
    {
        return new EzSystemsCommentsExtension(
            array(
                new Common(),
                new DisqusConfigParser(),
                new Facebook()
            )
        );
    }
}
