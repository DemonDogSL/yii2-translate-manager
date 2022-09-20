# Demon Dog SL - Translate Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/demondogsl/yii2-translate-manager.svg?style=flat)](https://packagist.org/packages/demondogsl/yii2-translate-manager)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/demondogsl/yii2-translate-manager.svg?style=flat)](https://packagist.org/packages/demondogsl/yii2-translate-manager)

This Extension is based in [lajax/yii2-translate-manager](https://github.com/lajax/yii2-translate-manager) that was ported for works with Bootstrap 5 by [KrishDemonDog](https://github.com/KrishDemonDog).

## Introduction

This extension provides a simple translating interface for the multilingual elements of your project. This extension offers:

- **Scan** - Automatically detect new elements of the language. Duplications are filtered out automatically during project scanning.
- **Optimize** - Unused language elements will be removed from the database.
- **Import** - Import your translations from `.json` or `.xml` file.
- **Export** - Export your translations to `.json` or `.xml` file.
- You can exclude files, folders or categories to prevent them from being translated.

## Contributing

Please read and follow the instructions in the [Contributing guide](CONTRIBUTING.md).

## Installation

Via [Composer](http://getcomposer.org/download/)

```
composer require demondogsl/yii2-translate-manager "*"
```

### Migration

Run the following command in Terminal for database migration:

```
php yii migrate --migrationPath vendor/demondogsl/yii2-translate-manager/migrations
```

### Config

Turn on the Translate Manager:
- In your project file, Yii2 Advanced `/backend/config/main.php` Yii2 Basic `/config/web.php` add Translate Manager in `modules`.

**IMPORTANT** Optional values overwrite Default Values.

```php
'modules' => [
    'translateManager' => [
        'class' => 'DemonDogSL\translateManager\Module',    // required
        'root' => '@example',   // optional     Directory of the project scan. Can be an Array with other instances. For example: ['@backend', '@common', '@frontend']. For Default is '@app'
        'allowedIPs' => ['0.0.0.0'],  // optional     IP addresses from which the translation interface is accessible. For Default is ['127.0.0.1']
        'roles' => ['@'],   // optional     For setting access levels to the translating interface. For Default is []
        'tmpDir' => '@example',     // optional     Writable directory for the client-side temporary javascript language files. Can be '@backend/runtime'. For Default is '@runtime'
        'ignoredCategories' => ['example'],     // optional     These categories won't be scanned. For Default is ['yii']
        'onlyCategories' => ['example'],    // optional     Only these categories will be scanned. For Default is []
        'ignoredItems' => ['example'],   // optional     These files will not be processed. For Default is ['.svn', '.git', '.gitignore', '.gitkeep', '.hgignore', '.hgkeep', '/messages', '/BaseYii.php', 'runtime', 'bower', 'nikic']
        'tables' => [   // optional     Database Tables that will be scanned. For Default not exists
            [
                'connection' => 'db',
                'table' => '{{%example}}',     // Table name
                'columns' => ['example1', 'example2'],    // Names of columns
                'category' => 'database-table-name',
            ]
        ],
    ],
],
```

- In our migration we added table `language_force_translation` for translate your ENUM values of your tables, if you want use it only add next code.
```php
'tables' => [
    [
        'connection' => 'db',
        'table' => 'language_force_translation',
        'columns' => ['value'],
        'category' => 'database-table-name'
    ]
]
```

- In your project file, Yii2 Advanced `/backend/config/main.php` Yii2 Basic `/config/web.php` add Translate Manager in `components`.

```php
'components' => [
    'translateManager' => [
        'class' => 'DemonDogSL\translateManager\Component'
    ]
]
```

- In your project file, Yii2 Advanced `/backend/config/main.php` Yii2 Basic `/config/web.php` add Translate Manager in `bootstrap`.

```php
'bootstrap' => [
    'translateManager' => [
        'class' => 'DemonDogSL\translateManager\Component'
    ],
],
```

- In your project file, Yii2 Advanced `/backend/config/main.php` Yii2 Basic `/config/web.php` add I18n in `components` and your `language`.

```php
'language' => 'en-GB',
'components' => [
    'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'yii\i18n\DbMessageSource',
                'db' => 'db',
                'sourceLanguage' => 'xx-XX',    // Your language
                'sourceMessageTable' => '{{%language_source}}',
                'messageTable' => '{{%language_translate}}',
                'cachingDuration' => 86400,
                'enableCaching' => true,
            ],
        ],
    ],
],
```

## Usage

### Register Translate Manager scripts

To translate static messages in JavaScript files it is necessary to register the files. You have two ways to do it.

Call the following method in each action:

```php
\DemonDogSL\translateManager\helpers\Language::registerAssets();
```

Or create a Controller that extends Yii Controller

**IMPORTANT** All your Controllers must extend this New Controller

```php
namespace backend\controllers;

use DemonDogSL\translateManager\helpers\Language;

class Controller extends \yii\web\Controller {

    public function init() {
        Language::registerAssets();
        parent::init();
    }
}
```

### Activate Translate Mode

This is Optional.

Display a button to switch text to translation mode.

```php
// Default
echo \DemonDogSL\translateManager\widgets\ToggleTranslate::widget();

// If you want change position
echo \DemonDogSL\translateManager\widgets\ToggleTranslate::widget([
    'position' => 'example',  // optional   Can be 'top-left', 'top-right', 'bottom-left' or 'bottom-right'. For Default is 'bottom-left'
]);
```

### Examples of use

- JavaScript

```php
ddt.t('Hello');
ddt.t('Hello {name}!', {name:'World'});
```

- PHP

```php
Yii::t('example', 'Hello');
Yii::t('example', 'Hello {name}!', ['name' => 'World']);
```

- Translate Mode Button

**IMPORTANT** Translate Mode Button does not support the translation of HTML attributes
```php
use DemonDogSL\translateManager\models\DDT;

DDT::t('example', 'Hello');
DDT::t('example', 'Hello {name}!', ['name' => 'World']);
```

### Translate Manager URL

```php
/translateManager/language/list
/translateManager/language/scan
/translateManager/language/optimize
/translateManager/language/import
/translateManager/language/export
```

## Change log

Please see [Changelog](CHANGELOG.md) for more information on what has changed recently.

## License
Same licence as [lajax/yii2-translate-manager](https://github.com/lajax/yii2-translate-manager)

The MIT License (MIT). Please see [License](LICENSE.md) for more information.

# Screenshots

### Languages
![Languages](/assets/images/Languages.png)

### Scan
![Scan](/assets/images/Scan.png)

### Optimize
![Optimize](/assets/images/Optimize.png)

### Export
![Export](/assets/images/Export.png)

### Translate Interface
![Translate_Interface](/assets/images/Translate_Interface.png)


### Translate Mode Button
![Translate_Mode_Button](/assets/images/Translate_Mode_Button.png)


### Translate Mode Button Translation
![Translate_Mode_Button_Translation](/assets/images/Translate_Mode_Button_Translation.png)

# Links

- [Original Translate Manager](https://github.com/lajax/yii2-translate-manager)
- [Packagist](https://packagist.org/packages/demondogsl/yii2-translate-manager)
- [Yii Extensions](http://www.yiiframework.com/extension/yii2-translate-manager)