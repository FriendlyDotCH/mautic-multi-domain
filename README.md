# Mautic Multi Domain Plugin creates the possibility to add tracking domains to Mautic

# Key features

Replaces the tracking domain in your emails based on the sender email address.

The plugin replaces list unsubscribe, image pixel, webview and unsubscribe tokens.

This plugin also rewrites all the tracking javascript domains to the domain from the http request e.g. if your Mautic domain is https://mautic.example.com and tracking js is loaded from from a CNAME, https://trk.example.net all of the domains used inside the tracking javascript will be `trk.example.net` (rather than `mautic.example.com`) to avoid the appearance of third-paty requests.

# Installation

```shell
# add the plugin to your project composer.json
composer require icecubenz/mauticmultidomain
# clear cache
php bin/console cache:clear --env=prod # or delete var/cache/prod/*
```

## Manual Installation

* Upload the zip package to your `plugins/`
* Unzip
* Rename the plugin folder to `MauticMultiDomainBundle`
* Refresh plugins


# Next steps

Create option to change URL for images as well.

# Credits

This bundle is a fork of https://github.com/friendly-ch/mautic-multi-domain - the vast majority of code comes from that project and it's respective authors.

This fork adds some additional features and changes behaviour based on my specific use cases.

As such no backwards compatibility is implied or guaranteed.
