# EzSystemsCommentsBundle configuration

## General consideration
EzSystemsCommentsBundle provides a semantic configuration (to be included in your `ezpublish.yml` or `config.yml`.

It is *siteaccess aware*, meaning that you can have several types of configuration depending on your *siteaccess*.

By default the **`no_comments`** provider is used, meaning that no comments will be displayed by default.
This lets you write versatile templates.

### Configuration overview

```yaml
ez_comments:
    system:
        siteaccess_name:
            # Comments provider you want to use by default (e.g. "disqus").
            default_provider: provider_name
            provider_name:
                # Here comes provider's configuration
```

> siteaccess_name can also be the name of a siteaccess group, "default" or "global"

Note that you can see dump configuration reference using the following command:

```bash
$ php ezpublish/console config:dump-reference ez_comments
```

> Default settings can be found in [`Resources/config/default_settings.yml`](Resources/config/default_settings.yml)

## Provider configuration

### Disqus
[Disqus](https://disqus.com) is a very powerful cloud-based commenting system.

The first thing you need to do is to [register your website to Disqus](https://disqus.com/admin/signup/?utm_source=New-Site).
You'll pick a *shortname* up, which is your website's identifier on Disqus (see more on [Disqus documentation](http://help.disqus.com/customer/portal/articles/466208-what-s-a-shortname-))

#### Configuration reference

```yaml
ez_comments:
    system:
        siteaccess_name:
            disqus:
                # Disqus "shortname"
                shortname: my_disqus_shortname # Required

                # Template to use, overriding the built-in one.
                # Default template is "EzSystemsCommentsBundle::disqus.html.twig"
                template:             ~
```

#### Available options
* `shortname`
* `identifier`
* `title`
* `url` (must be absolute, defaults to `window.location`)
* `categoryId`
* `template`

### Facebook
Facebook provider is an integration of [Facebook Comments](https://developers.facebook.com/docs/reference/plugins/comments/)

You first need to [register an application on Facebook](https://developers.facebook.com/apps/).
Once registered, you can get your **AppId** that will be used for configuration.

#### Configuration reference
Note that settings below are **default values** for your siteaccess.
You can override them from the comments renderer an the template helper.

```yaml
ez_comments:
    system:
        siteaccess_name:
            facebook:
                # Facebook application ID
                app_id:               ~ # Required

                # Width for the comments box (default is 470)
                width:                ~

                # Number of comments to display (default is 10)
                num_posts:            ~

                # Color scheme to use (can be "light" or "dark". Default is "light"
                color_scheme:         ~

                # Whether to include Facebook JS SDK with the comments rendering. If set to false, you must include it on your own. Default is true.
                include_sdk:          ~

                # Template to use, overriding the built-in one.
                # Default template is "EzSystemsCommentsBundle::facebook.html.twig"
                template:             ~
```

#### Available options
* `url` (must be absolute). Defaults to current main location URL when using Content, current URL otherwise.
* `width`
* `num_posts`
* `color_scheme`
* `include_sdk`
* `template`
