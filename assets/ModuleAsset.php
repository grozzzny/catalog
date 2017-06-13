<?php
namespace grozzzny\catalog\assets;

class ModuleAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@grozzzny/catalog/media';

    public $css = [];

    public $js = [
        'js/admin_module.js'
    ];

    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
}
