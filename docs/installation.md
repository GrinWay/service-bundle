Installation
------

1. Execute (for `vendor` dependencies)

```console
composer require grinway/service-bundle
```

> NOTE: With the help of the composer recipe you will get<br>`config/packages/service_bundle.yaml`
> <br>**Check it's not empty!**

If you didn't get these configuration files just copy them from `@GrinWayService/.install/symfony/config`

2. Add this to your `bundles.php`

```php
<?php

// %kernel.project_dir%/config/bundles.php
return [
    GrinWay\Service\GrinWayServiceBundle::class => ['all' => true],
];
```

[//]: # (3. Execute &#40;for `node_modules` dependencies&#41;)

[//]: # (```console)
[//]: # (yarn install --force)
[//]: # (```)

3. Set all ENV variables of this bundle (required by the `config/packages/grinway_service.yaml` file):

```env
###> grinway/service-bundle ###

#
# to be more secure
# set this to the symfony secrets https://symfony.com/doc/current/configuration/secrets.html
#
APP_CURRENCY_FIXER_API_KEY=

###< grinway/service-bundle ###
```
