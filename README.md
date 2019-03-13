# EzSystemsCommentsBundle

[![Build Status](https://img.shields.io/travis/ezsystems/CommentsBundle.svg?style=flat-square&branch=master)](https://travis-ci.org/ezsystems/CommentsBundle)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/comments-bundle.svg?style=flat-square)](https://packagist.org/packages/ezsystems/comments-bundle/stats)
[![Latest version](https://img.shields.io/github/release/ezsystems/CommentsBundle.svg?style=flat-square)](https://github.com/ezsystems/CommentsBundle/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/comments-bundle.svg?style=flat-square)](LICENSE)


CommentsBundle is an versatile bundle for eZ Platform *(v6 for eZ Platform and v5 for eZ Publish Platform 5.x)* making commenting easy.

## Install
1. Run: `composer require ezsystems/comments-bundle:^6.0`
2. Add `new EzSystems\CommentsBundle\EzSystemsCommentsBundle()` to acive prod bundles in `app/AppKernel.php`.

## Features

### Comments abstraction 
Many commenting systems exist across the web, *application specific* (such as
[FOSCommentBundle](https://github.com/FriendsOfSymfony/FOSCommentBundle)) or *cloud based* 
(such as [Disqus](http://disqus.com) or [Facebook Comments](https://developers.facebook.com/docs/reference/plugins/comments/)).

CommentsBundle is **provider based**. This means that it is open to **any kind of commenting system**.

### Single entry point
Render your comments with a single line of code.

## Available integration

CommentsBundle currently works with the following commenting systems:
* [Disqus](Resources/doc/02-configuration.md#disqus)
* [Facebook comments](Resources/doc/02-configuration.md#facebook)

## Documentation

Documentation can be found in `Resources/doc/` folder.

[Read the documentation](Resources/doc/index.md).

## License

This bundle is under **[GPL v2.0 license](http://www.gnu.org/licenses/gpl-2.0.html)**.
