Catalog for easyii2CMS 
==============================

This module allows to [Easy yii2 cms](http://github.com/noumo/easyii2) 

Demo: [salesmarket.org](https://salesmarket.org/) 

## Installation guide

```bash
$ php composer.phar require grozzzny/catalog "dev-master"
```


Run migrations
```bash
php yii migrate --migrationPath=@vendor/grozzzny/catalog/migrations
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
    'select_category' => Yii::t('gr','Select category..'),
    'key' => Yii::t('gr','Key'),
    'value' => Yii::t('gr','Value'),
    ...
    'filter_show' => Yii::t('gr','Show in filter'),
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
