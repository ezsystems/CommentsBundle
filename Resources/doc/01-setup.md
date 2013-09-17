# EzSystemsCommentsBundle installation

# A) Download and install EzSystemsCommentsBundle

To install EzSystemsCommentsBundle run the following command

```bash
$ php composer.phar require ezsystems/comments-bundle
```

# B) Enable the bundle

Enable EzSystemsCommentsBundle in the kernel:

```php
// ezpublish/EzPublishKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EzSystems\CommentsBundle\EzSystemsCommentsBundle(),
    );
}
```

### Continue to the next step
When you're done. Continue by configuring your comments providers:
[Step 2: Configure your comments providers](02-configuration.md).