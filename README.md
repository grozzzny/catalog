Catalog module for Yii2
==============================

This module allows to [yii2](https://www.yiiframework.com/) 

## Installation guide

```bash
$ php composer.phar require grozzzny/catalog "v3.x-dev"
```


Run migrations
```bash
php yii migrate --migrationPath=@vendor/grozzzny/catalog/migrations
```

Or add following lines to your console configuration file:

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationPath' => [
            '@grozzzny/catalog/migrations',
        ],
    ],
],
```

```php

'modules' => [
    'catalog' => [
        'class' => 'grozzzny\catalog\CatalogModule',
    ],
],
'i18n' => [
    'translations' => [
        'catalog' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@grozzzny/catalog/messages',
        ],
    ],
],
'container' => [
    'singletons' => [
        'grozzzny\catalog\models\Category' => ['class' => 'app\models\Category'],
    ],
],
```

## Schema
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_08-59-16.png)

## Catalog with different types of data
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-08-04.png)

## Any level of nesting
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-11-42.png)

## Speed filter
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-48-14.png)
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-50-45.png)

## Several languages
```php
<?
$i18n = json_encode([
    'select_category' => Yii::t('catalog','Select category..'),
    'key' => Yii::t('catalog','Key'),
    'value' => Yii::t('catalog','Value'),
    ...
    'filter_show' => Yii::t('catalog','Show in filter'),
], JSON_UNESCAPED_UNICODE);
```

## Element "Many to Many"
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-59-01.png)

## Inheritability of properties
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-15-52.png)
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-16-30.png)

## Large selection of property types
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-18-47.png)

## Convenient setting options for a property with the type "select"
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-20-56.png)

## Easily add customizations
```php
if ($property->settings->filter_range){
  ...
}
```
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-23-00.png)

## Universal validation rules
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-27-56.png)
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-29-15.png)

## Property type "Multi category"
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-31-50.png)
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-32-25.png)
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-33-28.png)

## Property type "HTML"
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-37-59.png)

## Property type "Image" or "File"
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-40-39.png)

## Property type "Code"
![alt text](https://raw.githubusercontent.com/grozzzny/catalog/master/media/2017-07-17_09-45-05.png)
