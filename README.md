# Mautic Multi Domain Plugin creates the possibility to add tracking domains to Mautic

# Key features

- Replaces the tracking domain in your emails based on the sender email address.
- The plugin replaces list unsubscribe, image pixel, webview and unsubscribe tokens.
- This plugin also rewrites all the tracking javascript domains to the domain from the http request
if your Mautic domain is https://mautic.example.com and tracking js is loaded from from a CNAME, https://trk.example.net all of the domains used inside the tracking javascript will be `trk.example.net` (rather than `mautic.example.com`) to avoid the appearance of third-paty requests.

# What does it do and why you need it:
https://www.youtube.com/watch?v=O8_pcHMXV-M


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

- Create option to change URL for images as well.
- Better how to guides

# Credits

Original work: https://github.com/friendly-ch/mautic-multi-domain
Amazing upgrade done by: https://github.com/rjocoleman
