<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\services;

use DemonDogSL\translateManager\models\TranslateManagerTemp;
use Yii;
use DemonDogSL\translateManager\models\LanguageSource;

class Scanner {

    public $scanners = [];

    const CATEGORY_JAVASCRIPT = 'javascript';
    const CATEGORY_ARRAY = 'array';
    const CATEGORY_DATABASE = 'database';

    private $_languageElements = [];

    /**
     * @return int The number of new language elements.
     */
    public function run() {
        $scanTimeLimit = Yii::$app->getModule('translateManager')->scanTimeLimit;
        if (!is_null($scanTimeLimit)) {
            set_time_limit($scanTimeLimit);
        }
        $scanners = Yii::$app->getModule('translateManager')->scanners;
        if (!empty($scanners)) {
            $this->scanners = $scanners;
        }
        $this->_scanningProject();
    }

    private function _scanningProject() {
        foreach ($this->scanners as $scanner) {
            $object = new $scanner($this);
            $object->run('');
        }
    }

    /**
     * @param string $category
     * @param string $message
     */
    public function addLanguageItem($category, $message) {
        $this->_languageElements[$category][$message] = true;
        // CHECK TRANSLATIONS
        $languageSource = LanguageSource::findOne(['category' => $category, 'message' => $message]);
        $newTranslations = TranslateManagerTemp::findOne(['setting' =>'newTranslations']);
        $existTranslations = TranslateManagerTemp::findOne(['setting' =>'existTranslations']);
        if (!isset($newTranslations)) {
            $newTranslations = new TranslateManagerTemp();
            $newTranslations->setting = 'newTranslations';
            $newTranslations->value = json_encode([]);
            $newTranslations->save();
        }
        if (!isset($existTranslations)) {
            $existTranslations = new TranslateManagerTemp();
            $existTranslations->setting = 'existTranslations';
            $existTranslations->value = json_encode([]);
            $existTranslations->save();
        }
        // NEW TRANSLATIONS
        $nTValueDecoded = json_decode($newTranslations->value);
        $eTValueDecoded = json_decode($existTranslations->value);
        if (!isset($languageSource)) {
            $languageSource = new LanguageSource();
            $languageSource->category = $category;
            $languageSource->message = $message;
            $languageSource->save();
            if ($languageSource->id !== null) {
                $nTValueDecoded[] = $languageSource->id;
                $newTranslations->value = json_encode($nTValueDecoded);
                $newTranslations->save();
            }
        }
        // EXIST TRANSLATIONS
        if (!in_array($languageSource->id, $eTValueDecoded)) {
            if ($languageSource->id !== null) {
                $eTValueDecoded[] = $languageSource->id;
                $existTranslations->value = json_encode($eTValueDecoded);
                $existTranslations->save();
            }
        }
    }

    /**
     * @param array $languageItems
     */
    public function addLanguageItems($languageItems) {
        foreach ($languageItems as $languageItem) {
            $this->addLanguageItem($languageItem['category'], $languageItem['message']);
        }
    }

}