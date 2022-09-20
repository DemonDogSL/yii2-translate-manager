<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\bundles;

use yii\web\AssetBundle;

class TranslatePluginAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@DemonDogSL/translateManager/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'javascripts/helpers.js',
        'javascripts/translate.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'DemonDogSL\translateManager\bundles\TranslationPluginAsset',
    ];

}