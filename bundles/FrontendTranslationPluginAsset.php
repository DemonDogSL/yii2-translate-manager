<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\bundles;

use yii\web\AssetBundle;

class FrontendTranslationPluginAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@DemonDogSL/translateManager/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'javascripts/helpers.js',
        'javascripts/frontend-translation.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'DemonDogSL\translateManager\bundles\TranslationPluginAsset',
    ];

}