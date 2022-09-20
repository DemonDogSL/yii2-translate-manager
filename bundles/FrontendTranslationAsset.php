<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\bundles;

use yii\web\AssetBundle;

class FrontendTranslationAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@DemonDogSL/translateManager/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'stylesheets/helpers.css',
        'stylesheets/frontend-translation.css',
    ];

}