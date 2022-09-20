<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use DemonDogSL\translateManager\helpers\Language;
use DemonDogSL\translateManager\models\LanguageSource;
use DemonDogSL\translateManager\models\LanguageTranslate;

class TranslateBehavior extends AttributeBehavior {

    public $translateAttributes;
    public $category = 'database';
    public $owner;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->category = str_replace(['{', '%', '}'], '', $this->category);
    }

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'translateAttributes',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'saveAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'saveAttributes',
        ];
    }

    /**
     * @param \yii\base\Event $event
     */
    public function translateAttributes($event) {
        foreach ($this->translateAttributes as $attribute) {
            $this->owner->{$attribute} = Yii::t($this->category, $this->owner->attributes[$attribute]);
        }
    }

    /**
     * @param \yii\base\Event $event
     */
    public function saveAttributes($event) {
        $isAppInSourceLanguage = Yii::$app->sourceLanguage === Yii::$app->language;
        foreach ($this->translateAttributes as $attribute) {
            if (!$this->owner->isAttributeChanged($attribute)) {
                continue;
            }
            if ($isAppInSourceLanguage || !$this->saveAttributeValueAsTranslation($attribute)) {
                Language::saveMessage($this->owner->attributes[$attribute], $this->category);
            }
        }
    }

    /**
     * @param string $attribute The name of the attribute.
     * @return bool Whether the translation is saved.
     */
    private function saveAttributeValueAsTranslation($attribute) {
        $sourceMessage = $this->owner->getOldAttribute($attribute);
        $translatedMessage = $this->owner->attributes[$attribute];
        $this->owner->{$attribute} = $sourceMessage;
        $translateSource = $this->findSourceMessage($sourceMessage);
        if (!$translateSource) {
            return false;
        }
        $translation = new LanguageTranslate();
        foreach ($translateSource->languageTranslates as $tmpTranslate) {
            if ($tmpTranslate->language === Yii::$app->language) {
                $translation = $tmpTranslate;
                break;
            }
        }
        if ($translation->isNewRecord) {
            $translation->id = $translateSource->id;
            $translation->language = Yii::$app->language;
        }
        $translation->translation = $translatedMessage;
        $translation->save();
        return true;
    }

    /**
     * @param string $message
     * @return LanguageSource|null Null if the source is not found.
     */
    private function findSourceMessage($message) {
        $sourceMessages = LanguageSource::findAll(['message' => $message, 'category' => $this->category]);
        foreach ($sourceMessages as $source) {
            if ($source->message === $message) {
                return $source;
            }
        }
    }

}