grinway/service-bundle
========

# Description


This bundle provides ready to use services:
| Service id |
| ------------- |
| grin_way_service.faker |
| grin_way_service.carbon_factory_immutable |
| [GrinWay\Service\Service\ArrayService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/ArrayService.php) |
| [GrinWay\Service\Service\BoolService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/BoolService.php) |
| [GrinWay\Service\Service\BufferService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/BufferService.php) |
| [GrinWay\Service\Service\CarbonService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/CarbonService.php) |
| [GrinWay\Service\Service\ClipService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/ClipService.php) |
| [GrinWay\Service\Service\ConfigService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/ConfigService.php) |
| [GrinWay\Service\Service\DumpInfoService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/DumpInfoService.php) |
| [GrinWay\Service\Service\FilesystemService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/FilesystemService.php) |
| [GrinWay\Service\Service\HtmlService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/HtmlService.php) |
| [GrinWay\Service\Service\OSService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/OSService.php) |
| [GrinWay\Service\Service\ParserService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/ParserService.php) |
| [GrinWay\Service\Service\RandomPasswordService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/RandomPasswordService.php) |
| [GrinWay\Service\Service\RegexService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/RegexService.php) |
| [GrinWay\Service\Service\StringService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/StringService.php) |
| [GrinWay\Service\Service\DoctrineService](https://github.com/GrinWay/service-bundle/blob/main/src/Service/DoctrineService.php) |

# Installation

### Step 1: Require the bundle

In your `%kernel.project_dir%/composer.json`

```json
"require": {
	"grinway/service-bundle": "VERSION"
},
"repositories": [
	{
		"type": "path",
		"url": "./bundles/grinway/service-bundle"
	}
]
```

### Step 2: Download the bundle

### [Before git clone](https://github.com/GrinWay/docs/blob/main/docs/bundles_grin_symfony%20mkdir.md)

```console
git clone "https://github.com/GrinWay/service-bundle.git"
```

```console
cd "../../"
```

```console
composer require "grinway/service-bundle"
```

### [Binds](https://github.com/GrinWay/docs/blob/main/docs/borrow-services.yaml-section.md)

### Step 3: Usage

**Symfony Autowiring**

These services are already available for using:

```php
namespace YourNamespace;

use GrinWay\Service\Service\StringService;

class YourClass {
	public function __construct(
		private readonly StringService $stringService,
	) {}

	public function yourMethod() {
		return $this->stringService->SOME_METHOD();
	}
}
```

**php extending + Symfony Autowiring**

```php
//###> YOUR FILE #1 ###

namespace App\Service;

use GrinWay\Service\Service\StringService as GrinWayStringService;

class StringService extend GrinWayStringService {}


//###> YOUR FILE #2 ###

use App\Service\StringService;

class YourClass {
	public function __construct(
		private readonly StringService $stringService,
	) {}

	public function yourMethod() {
		return $this->stringService->SOME_METHOD();
	}
}
```

**Or bind grin_way_service services**

```yaml
parameters:

services:
    _defaults:
        autowire: true
        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.
    
        bind:
            ###> SERVICES ###
            
            $t:                 '@Symfony\Contracts\Translation\TranslatorInterface'
            
            ###>grin_way_service ###
            
            # ___FOUND THEM BY EXECUTING___: php.exe ./bin/console debug:container | grep grin_way_service
            # ___OR FOR LINUX CLI___: bin/console debug:container | grep grin_way_service
            
            $carbonFactoryImmutable: '@grin_way_service.carbon_factory_immutable'
            $faker: '@grin_way_service.faker'
            ###< grin_way_service ###
            
            ###< SERVICES ###
```

```php
//###> YOUR FILE ###

use Symfony\Component\Routing\Attribute\Route;

class YourController {

	#[Route(path: '/')]
	public function home(
		$faker, // BIND AUTOWIRING for @grin_way_service.faker!
	) {
		return $this->render('home/home.html.twig', [
			'random_number' => $faker->numberBetween(0, 1000),
		]);
	}
}
```

### Step 4: Override bundle parameters and configure the bundle

Open terminal in your project `%kernel.project_dir%` and execute:

```console
cp "./bundles/grinway/service-bundle/config/packages/grin_way_service.yaml" "./config/packages/grin_way_service.yaml"
```

Here `%kernel.project_dir%/config/packages/grin_way_service.yaml`, you can override any parameter.

Also you can override parameters in your `%kernel.project_dir%/config/services.yaml`

```yaml
parameters:

    # ___FOUND THEM BY EXECUTING___: php.exe ./bin/console debug:container --parameters | grep grin_way_service
    # ___OR FOR LINUX CLI___: bin/console debug:container --parameters | grep grin_way_service

    ###> GrinWay\Service ###
    grin_way_service.locale:                         '%grin_way_service.locale%'
    grin_way_service.timezone:                       '%grin_way_service.timezone%'
    grin_way_service.app_env:                        '%grin_way_service.app_env%'
    grin_way_service.local_drive_for_test:           '%grin_way_service.local_drive_for_test%'
    grin_way_service.year_regex:                     '%grin_way_service.year_regex%'
    grin_way_service.year_regex_full:                '%grin_way_service.year_regex_full%'
    grin_way_service.ip_v4_regex:                    '%grin_way_service.ip_v4_regex%'
    grin_way_service.slash_of_ip_regex:              '%grin_way_service.slash_of_ip_regex%'
    grin_way_service.start_of_win_sys_file_regex:    '%grin_way_service.start_of_win_sys_file_regex%'
    
    
    # To get with \GrinWay\Service\Service\ConfigService::getPackageValue(without arguments)
    # the following result:
    #    array:2 [
    #       "config/packages/framework.yaml" => array:2 []
    #       "config/packages/grin_way_service.yaml" => []
    #   ]
    grin_way_service.load_packs_configs:
        -   pack_name:      'framework.yaml'
            pack_rel_path:  'config/packages'
            lazy_load:      false
        -   pack_name:      'grin_way_service.yaml'
            pack_rel_path:  'config/packages'
            does_not_exist_mess: "This package does't exist!"
        -   pack_name:      'cache.yaml'
            pack_rel_path:  'config/packages'
            lazy_load:      true
    ###< GrinWay\Service ###
```

But remember, `services.yaml` parameters wins `grin_way_service.yaml` ones.