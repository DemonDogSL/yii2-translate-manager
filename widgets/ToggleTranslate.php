<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\widgets;

use Yii;
use yii\base\Widget;
use DemonDogSL\translateManager\Module;

class ToggleTranslate extends Widget {

    const DIALOG_URL = '/translateManager/language/dialog';
    const POSITION_TOP_LEFT = 'top-left';
    const POSITION_TOP_RIGHT = 'top-right';
    const POSITION_BOTTOM_LEFT = 'bottom-left';
    const POSITION_BOTTOM_RIGHT = 'bottom-right';
    public $position = 'bottom-left';
    public $template = '<a href="javascript:void(0);" id="toggle-translate" class="{position} btn mb-3 text-dark" data-language="{language}" data-url="{url}" style="z-index: 999999; margin-left: 1em !important;"><i></i> {text}</a><div id="translate-manager-div"></div>';
    public $frontendTranslationAsset = 'DemonDogSL\translateManager\bundles\FrontendTranslationAsset';
    public $frontendTranslationPluginAsset = 'DemonDogSL\translateManager\bundles\FrontendTranslationPluginAsset';

    /**
     * @inheritdoc
     */
    public function run() {
        if (!Yii::$app->session->has(Module::SESSION_KEY_ENABLE_TRANSLATE)) {
            return;
        }
        $this->_registerAssets();
        echo strtr($this->template, [
            '{text}' => Yii::t('language', 'Translate Mode'),
            '{position}' => $this->position,
            '{language}' => Yii::$app->language,
            '{url}' => self::DIALOG_URL,
        ]);
    }

    private function _registerAssets() {
        if ($this->frontendTranslationAsset) {
            Yii::$app->view->registerAssetBundle($this->frontendTranslationAsset);
        }
        if ($this->frontendTranslationPluginAsset) {
            Yii::$app->view->registerAssetBundle($this->frontendTranslationPluginAsset);
        }
    }

}