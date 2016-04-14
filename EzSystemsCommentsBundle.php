<?php

/**
 * File containing the EzSystemsCommentsBundle class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\CommentsBundle;

use EzSystems\CommentsBundle\DependencyInjection\Compiler\ProviderPass;
use EzSystems\CommentsBundle\DependencyInjection\EzSystemsCommentsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsCommentsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProviderPass());
        parent::build($container);
    }

    public function getContainerExtension()
    {
        return new EzSystemsCommentsExtension();
    }
}
