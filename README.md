# CommentsBundle

CommentsBundle is an versatile extension for eZ Publish 5+ making commenting easy.

## Features

### Comments abstraction 
Many commenting systems exist across the web, *application specific* (such as
[FOSCommentsBundle](https://github.com/FriendsOfSymfony/FOSCommentBundle)) or *cloud based* 
(such as [Disqus](http://disqus.com) or [Facebook Comments](https://developers.facebook.com/docs/reference/plugins/comments/)).

CommentsBundle is **provider based**. This means that it is open to **any kind of commenting system**.

### Single entry point
Render your comments with a single line of code.

#### Rendering comments from an eZ Content
Useful if you want to attach comments to a given content object.

**Twig**

```jinja
{{ ez_comments_render_content( content.contentInfo ) }}
```

**PHP**

```php
/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
/** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
$commentsRenderer = $container->get( 'ez_comments.renderer' );
$commentsRenderer->renderForContent( $contentInfo );
```

#### Rendering comments for current URL
Useful if your comments are based on the current request (e.g. from a custom controller).

**Twig**

```jinja
{{ ez_comments_render() }}
```

**PHP**
```php
/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$commentsRenderer = $container->get( 'ez_comments.renderer' );
$commentsRenderer->render();
```

## Available integration

CommentsBundle currently works with the following commenting systems:
* Disqus
* Facebook comments

