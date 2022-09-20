<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\bundles;

use yii\web\AssetBundle;

class TranslationPluginAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@DemonDogSL/translateManager/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'javascripts/md5.js',
        'javascripts/ddt.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'DemonDogSL\translateManager\bundles\LanguageItemPluginAsset',
    ];

}