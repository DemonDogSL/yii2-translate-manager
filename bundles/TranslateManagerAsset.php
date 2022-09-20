<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\bundles;

use yii\web\AssetBundle;

class TranslateManagerAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@DemonDogSL/translateManager/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'stylesheets/translate-manager.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

}