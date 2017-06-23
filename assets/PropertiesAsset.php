<?php
namespace grozzzny\catalog\assets;

class PropertiesAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@grozzzny/catalog/media';

    public $css = [];

    public $js = [
        'js/properties.js',
    ];

    public $jsOptions = array(
        'position' => \yii\web\View::POS_END
    );
}
