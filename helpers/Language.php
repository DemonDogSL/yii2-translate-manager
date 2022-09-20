<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\helpers;

use DemonDogSL\translateManager\services\Generator;
use DemonDogSL\translateManager\models\LanguageSource;
use DemonDogSL\translateManager\Module;
use Yii;
use DemonDogSL\translateManager\services\Scanner;
use DemonDogSL\translateManager\bundles\TranslationPluginAsset;
use yii\bootstrap5\Html;
use yii\helpers\Json;

class Language {

    private static $_template = '<span class="language-item" data-category="{category}" data-hash="{hash}" data-language_id="{language_id}" data-params="{params}">{message}</span>';

    public static function registerAssets() {
        // GENERATE FILE JS AND DELETE CACHE FOLDER IN runtime WITH JS TRANSLATIONS
        if (is_dir(__DIR__ . '/../../../../backend') === true) {
            if (is_dir(__DIR__ . '/../../../../backend/runtime/cache') === true) {
                self::removeDir(__DIR__ . '/../../../../backend/runtime/cache');
            }
            if (file_exists(__DIR__ . '/../../../../backend/runtime/translate/' . Yii::$app->language . '.js') === false) {
                $module = Yii::$app->getModule('translateManager');
                $generator = new Generator($module, Yii::$app->language);
                $generator->run();
            }
        } else {
            if (is_dir(__DIR__ . '/../../../../runtime/cache') === true) {
                self::removeDir(__DIR__ . '/../../../../runtime/cache');
            }
            if (file_exists(__DIR__ . '/../../../../runtime/translate/' . Yii::$app->language . '.js') === false) {
                $module = Yii::$app->getModule('translateManager');
                $generator = new Generator($module, Yii::$app->language);
                $generator->run();
            }
        }
        TranslationPluginAsset::register(Yii::$app->view);
    }

    /**
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`).
     * @return string the translated message.
     */
    public static function t($category, $message, $params = [], $language = null) {
        if (self::isEnabledTranslate()) {
            return strtr(self::$_template, [
                '{language_id}' => $language ? $language : Yii::$app->language,
                '{category}' => $category,
                '{message}' => Yii::t($category, $message, $params, $language),
                '{params}' => Html::encode(Json::encode($params)),
                '{hash}' => md5($message),
            ]);
        } else {
            return Yii::t($category, $message, $params, $language);
        }
    }

    /**
     * @param array $array One-dimensonal or Multi-dimensional array to be translated.
     * @param array $params List of parameters to be changed.
     * @param string $language Language of translation.
     * @return array The translated array.
     */
    public static function a($array, $params = [], $language = null) {
        $data = [];
        foreach ($array as $key => $message) {
            if (!is_array($message)) {
                $data[$key] = Yii::t(Scanner::CATEGORY_ARRAY, $message, isset($params[$key]) ? $params[$key] : [], $language);
            } else {
                $data[$key] = self::a($message, isset($params[$key]) ? $params[$key] : [], $language);
            }
        }
        return $data;
    }

    /**
     * @param string $message Language element stored in database.
     * @param array $params Parameters to be changed.
     * @param string $language Language of translation.
     * @return string Translated language element.
     */
    public static function d($message, $params = [], $language = null) {
        return Yii::t(Scanner::CATEGORY_DATABASE, $message, $params, $language);
    }

    /**
     * @return bool
     */
    public static function isEnabledTranslate() {
        return Yii::$app->session->has(Module::SESSION_KEY_ENABLE_TRANSLATE);
    }

    /**
     * @param string $message Language element save in database.
     * @param string $category the message category.
     */
    public static function saveMessage($message, $category = 'database') {
        $languageSources = LanguageSource::find()->where(['category' => $category])->all();
        $messages = [];
        foreach ($languageSources as $languageSource) {
            $messages[$languageSource->message] = $languageSource->id;
        }
        if (empty($messages[$message])) {
            $languageSource = new LanguageSource();
            $languageSource->category = $category;
            $languageSource->message = $message;
            $languageSource->save();
        }
    }

    /**
     * @return array
     */
    public static function getCategories() {
        $languageSources = LanguageSource::find()->select('category')->distinct()->all();
        $categories = [];
        foreach ($languageSources as $languageSource) {
            $categories[$languageSource->category] = $languageSource->category;
        }
        return $categories;
    }

    // DELETE CACHE FOLDER
    public static function removeDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                        self::removeDir($dir . DIRECTORY_SEPARATOR . $object);
                    } else {
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

}