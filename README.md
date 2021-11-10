# Mautic Multi Domain Plugin creates the possibility to add tracking domains to Mautic

# Key features
Replaces the tracking domain in your emails based on the sender email address
The plugin replaces list unsubscribe, image pixel, webview and unsubscribe tokens

# Installation
Upload the zip package to your plugins/ folder
Unzip
Refresh plugins

# Composer Installation (Mautic 4)
```shell
# add the plugin to your project composer.json
composer require friendlyis/mauticmultidomain
# clear cache
php bin/console cache:clear --env=prod
```

# Next steps
Create option to change URL for images as well
