# Mautic Multi Domain Plugin creates the possibility to add tracking domains to Mautic

```shell
@@@@@@@@@@@&#BGP55555555PGB#@@@@@@@@@@@@
@@@@@@@@#BP55PGGB######BGGP5#@@@@@@@@@@@
@@@@@&BP55G#&@@@@@@@@@@@@@@@@@@@@@@@@@@@
@@@@B55P#@@@@@@@@@@@@@@@@@@@#BGP5#@@@@@@
@@&P55#@@@@@@@@@@@@@@@@@@@@#7~~~~#@@@@@@
@&P5P&@@@@@@GP@@@@@@@@@@@&5!~~~!J@@@@&@@
@P5P&@@@@@@&!~7P&@@@@@@&5!~~~JB&&@@@G5P&
B55#@@@@@@@5~~~~7P&@@&5!~~~?B@@@@@@@#55G
P5P@@@@@@@&!~~~~~~7PP!~~~?B@B@@@@@@@@P55
55G@@@@@@@5~~~PB?~~~~~~?G@G7~B@@@@@@@G55
P5P@@@@@@&!~~7@@@G7~~7G@@#~~~?@@@@@@@P55
B55#@@@@@5~~~P@@@@@PP@@@@@Y~~~B@@@@@#55G
@P5P&@@@&!~~7@@@@@@@@@@@@@#~~~7@@@@@P5P&
@&P5P&@@#555B@@@@@@@@@@@@@@G555&@@&P55&@
@@&P55#@@@@@@@@@@@@@@@@@@@@@@@@@@#55P&@@
@@@@B55P#@@@@@@@@@@@@@@@@@@@@@@#P55B@@@@
@@@@@&BP55G#&@@@@@@@@@@@@@@&#G55PB&@@@@@
@@@@@@@@#BP55PPGB######BGGP55PB#@@@@@@@@
@@@@@@@@@@@&#BGP55555555PGB#&@@@@@@@B#GG
>>>>>>>>>>>>> JOS OF BHS <<<<<<<<<<<<<<<
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

#     *          (          (      (        )    *           (       )  
#   (  `         )\ )  *   ))\ )   )\ )  ( /(  (  `    (     )\ ) ( /(  
#   )\))(     ( (()/(` )  /(()/(  (()/(  )\()) )\))(   )\   (()/( )\()) 
#  ((_)()\    )\ /(_))( )(_))(_))  /(_))((_)\ ((_)()((((_)(  /(_)|(_)\  
#  (_()((_)_ ((_|_)) (_(_()|_))   (_))_   ((_)(_()((_)\ _ )\(_))  _((_) 
#  |  \/  | | | | |  |_   _|_ _|   |   \ / _ \|  \/  (_)_\(_)_ _|| \| | 
#  | |\/| | |_| | |__  | |  | |    | |) | (_) | |\/| |/ _ \  | | | .` | 
#  |_|  |_|\___/|____| |_| |___|   |___/ \___/|_|  |_/_/ \_\|___||_|\_| 
#                    *                       (                          
#   (              (  `    (            *   ))\ )  (                    
#   )\ )     (     )\))(   )\       ( ` )  /(()/(  )\                   
#  (()/(  (  )(   ((_)()((((_)(     )\ ( )(_))(_)|((_)                  
#   /(_)) )\(()\  (_()((_)\ _ )\ _ ((_|_(_()|_)) )\___                  
#  (_) _|((_)((_) |  \/  (_)_\(_) | | |_   _|_ _((/ __|                 
#   |  _/ _ \ '_| | |\/| |/ _ \ | |_| | | |  | | | (__                  
#   |_| \___/_|   |_|  |_/_/ \_\ \___/  |_| |___| \___|                 
#                                                                       

```

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

# API
in version 1.3 Amazing API upgrade from
https://github.com/MPCKowalski and https://github.com/cubitt0

REST API capability allows you the follwoings:

- Get info of a domain:
`GET /multidomain/ID`
- List all multidomain entries:
`GET /multidomain`
- Create new multidomain entry:
`POST /multidomain/new`
body parameters: email, domain
- Edit an existing multidomain entry:
`PUT /multidomain/ID/edit`
`PATCH /multidomain/ID/edit`
body parameters: email, domain
- Delete Multidomain entry:
`DEL /multidomain/ID/delete`

# Next steps

- Create option to change URL for images as well.
- Better how to guides

# Credits

Original work: https://github.com/friendly-ch/mautic-multi-domain
Amazing upgrade done by: https://github.com/rjocoleman
