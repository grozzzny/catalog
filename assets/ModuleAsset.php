<?php
namespace grozzzny\catalog\assets;

class ModuleAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@grozzzny/catalog/media';

    public $css = [
        'font-awesome-4.7.0/css/font-awesome.min.css',
    ];

    public $js = [
        'js/translit.js',
    ];

    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
}
